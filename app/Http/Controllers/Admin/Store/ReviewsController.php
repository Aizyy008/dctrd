<?php

namespace App\Http\Controllers\Admin\Store;

use App\Exports\ProductReviewExport;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Reward;
use App\Models\RewardAccounting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReviewsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_store_products_reviews');

        $query = ProductReview::query();

        $totalReviews = deepClone($query)->count();
        $publishedReviews = deepClone($query)->where('status', 'active')->count();
        $ratesAverage = deepClone($query)->avg('rates');
        $productsWithoutReview = Product::where('status', Product::$active)->whereDoesntHave('reviews')->count();

        $query = $this->filters($query, $request);

        $reviews = $query->orderBy('created_at', 'desc')
            ->with([
                'product' => function ($query) {
                    $query->select('id', 'slug');
                },
                'creator' => function ($query) {
                    $query->select('id', 'full_name');
                },
            ])
            ->withCount('comments')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.admin_store_reviews_list_title'),
            'totalReviews' => $totalReviews,
            'publishedReviews' => $publishedReviews,
            'ratesAverage' => round($ratesAverage, 2),
            'productsWithoutReview' => $productsWithoutReview,
            'reviews' => $reviews,
        ];

        $product_ids = $request->get('product_ids');
        if (!empty($product_ids)) {
            $data['products'] = Product::select('id')->whereIn('id', $product_ids)->get();
        }

        return view('admin.store.reviews.lists', $data);
    }

    private function filters($query, $request)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $search = $request->get('search', null);
        $product_ids = $request->get('product_ids');
        $status = $request->get('status', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($search)) {
            $query->where('description', 'like', "%$search%");
        }

        if (!empty($product_ids)) {
            $query->whereIn('product_id', $product_ids);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function toggleStatus($id)
    {
        $this->authorize('admin_store_products_reviews_status_toggle');

        $review = ProductReview::findOrFail($id);

        $review->update([
            'status' => ($review->status == 'active') ? 'pending' : 'active',
        ]);

        /*if ($review->status == 'active') {
            $reviewReward = RewardAccounting::calculateScore(Reward::REVIEW_COURSES);
            RewardAccounting::makeRewardAccounting($review->creator_id, $reviewReward, Reward::REVIEW_COURSES, $review->id, true);
        }*/

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => 'Review status changed successful',
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }

    public function reply(Request $request, $id)
    {
        $this->authorize('admin_reviews_reply');

        $review = ProductReview::findOrFail($id);

        $data = [
            'pageTitle' => trans('admin/pages/comments.reply_comment'),
            'review' => $review,
        ];

        return view('admin.store.reviews.comment_reply', $data);
    }

    public function delete($id)
    {
        $this->authorize('admin_store_products_reviews_delete');

        $review = ProductReview::findOrFail($id);

        $review->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => 'Review deleted successful',
            'status' => 'success'
        ];
        return back()->with(['toast' => $toastData]);
    }

    public function export()
    {
        return Excel::download(new ProductReviewExport, 'products_review.xlsx');
    }

    public function import(Request $request)
{
    $request->validate([
        'excel_file' => 'required|mimes:xlsx,xls,csv'
    ]);

    if (!$request->hasFile('excel_file')) {
        return back()->with('error', 'No file was uploaded.');
    }

    $file = $request->file('excel_file');

    try {
        $data = Excel::toArray([], $file)[0];

        if (empty($data) || count($data) < 2) {
            return back()->with('error', 'The uploaded file is empty or has an incorrect format.');
        }

        // Remove the header row
        array_shift($data);

        $errors = []; // Store validation errors

        DB::beginTransaction();
        try {
            foreach ($data as $index => $row) {
                if (count($row) < 5) {
                    $errors[] = "Row " . ($index + 2) . ": Missing required columns.";
                    continue;
                }

                $productId = (int) trim($row[0]);
                $creatorUserId = (int) trim($row[1]);
                $description = trim($row[2]);
                $rate = (int) trim($row[3]);
                $status = strtolower(trim($row[4]));

                // Validate product_id exists
                if (!DB::table('products')->where('id', $productId)->exists()) {
                    $errors[] = "Row " . ($index + 2) . ": Product ID {$productId} does not exist.";
                    continue;
                }

                // Validate creator_id exists
                if (!DB::table('users')->where('id', $creatorUserId)->exists()) {
                    $errors[] = "Row " . ($index + 2) . ": User ID {$creatorUserId} does not exist.";
                    continue;
                }

                // Validate rates (should be between 1-5)
                if ($rate < 1 || $rate > 5) {
                    $errors[] = "Row " . ($index + 2) . ": Invalid rating value '{$rate}'. Must be between 1 and 5.";
                    continue;
                }

                // Validate status (allow only "pending" or "active")
                if (!in_array($status, ['pending', 'active'])) {
                    $status = 'pending';
                }

                // Insert data
                ProductReview::create([
                    'product_id' => $productId,
                    'creator_id' => $creatorUserId,
                    'description' => $description,
                    'rates' => (string) $rate,
                    'created_at' => time(), // Store as UNIX timestamp
                    'status' => $status,
                ]);
            }

            // If validation errors exist, rollback and return all errors
            if (!empty($errors)) {
                DB::rollBack();
                return back()->with('error', implode('<br>', $errors));
            }

            DB::commit();
            return back()->with('success', 'Product reviews imported successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error inserting data: ' . $e->getMessage());
        }
    } catch (\Exception $e) {
        return back()->with('error', 'Error processing file: ' . $e->getMessage());
    }
}



    public function downloadTemplate()
    {
        $headers = ['Product ID', 'Customer ID', 'Comment', 'Rate (5)', 'Status'];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers in first row (columns A to E)
        foreach (range('A', 'E') as $index => $column) {
            $sheet->setCellValue($column . '1', $headers[$index]);
        }

        // Apply bold styling to headers only in A1:E1 (not A1:G1)
        $styleArray = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($styleArray);

        // Auto-size columns for A to E
        foreach (range('A', 'E') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'products_review_template.xlsx';
        $tempFilePath = storage_path('app/' . $fileName);
        $writer->save($tempFilePath);

        return response()->download($tempFilePath)->deleteFileAfterSend(true);
    }






}
