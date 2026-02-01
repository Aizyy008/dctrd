<?php

namespace App\Http\Controllers\Admin;

use App\Exports\WebinarsReviewExport;
use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Webinar;
use App\Models\WebinarReview;
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
        $this->authorize('admin_reviews_lists');

        $query = WebinarReview::query();

        $totalReviews = deepClone($query)->count();
        $publishedReviews = deepClone($query)->where('status', 'active')->count();
        $ratesAverage = deepClone($query)->avg('rates');
        $classesWithoutReview = Webinar::where('status', Webinar::$active)->whereDoesntHave('reviews')->count();

        $query = $this->filters($query, $request);

        $reviews = $query->orderBy('created_at', 'desc')
            ->with([
                'webinar' => function ($query) {
                    $query->select('id', 'slug');
                },
                'bundle' => function ($query) {
                    $query->select('id', 'slug');
                },
                'creator' => function ($query) {
                    $query->select('id', 'full_name');
                },
            ])
            ->withCount([
                'comments'
            ])
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.reviews_list_title'),
            'totalReviews' => $totalReviews,
            'publishedReviews' => $publishedReviews,
            'ratesAverage' => round($ratesAverage, 2),
            'classesWithoutReview' => $classesWithoutReview,
            'reviews' => $reviews,
        ];

        $webinar_ids = $request->get('webinar_ids');
        if (!empty($webinar_ids)) {
            $data['webinars'] = Webinar::select('id')->whereIn('id', $webinar_ids)->get();
        }

        return view('admin.reviews.lists', $data);
    }

    private function filters($query, $request)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $search = $request->get('search', null);
        $webinar_ids = $request->get('webinar_ids');
        $status = $request->get('status', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($search)) {
            $query->where('description', 'like', "%$search%");
        }

        if (!empty($webinar_ids)) {
            $query->whereIn('webinar_id', $webinar_ids);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function toggleStatus($id)
    {
        $this->authorize('admin_reviews_status_toggle');

        $review = WebinarReview::findOrFail($id);

        $review->update([
            'status' => ($review->status == 'active') ? 'pending' : 'active',
        ]);

        if ($review->status == 'active') {
            $reviewReward = RewardAccounting::calculateScore(Reward::REVIEW_COURSES);
            RewardAccounting::makeRewardAccounting($review->creator_id, $reviewReward, Reward::REVIEW_COURSES, $review->id, true);
        }

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

        $review = WebinarReview::findOrFail($id);

        $data = [
            'pageTitle' => trans('admin/pages/comments.reply_comment'),
            'review' => $review,
        ];

        return view('admin.reviews.comment_reply', $data);
    }

    public function delete($id)
    {
        $this->authorize('admin_reviews_status_toggle');

        $review = WebinarReview::findOrFail($id);

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
        return Excel::download(new WebinarsReviewExport, 'webinars_review.xlsx');
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

        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                // Ensure all required columns exist
                if (count($row) < 6) {
                    continue;
                }

                $webinarId = (int) trim($row[0]);
                $creatorUserId = (int) trim($row[1]);
                $type = trim($row[2]);
                $comment = trim($row[3]);
                $rate = (int) trim($row[4]);
                $status = strtolower(trim($row[5]));

                // Validate webinar_id & creator_user_id exist
                if (!DB::table('webinars')->where('id', $webinarId)->exists()) {
                    return back()->with('error', "Webinar ID {$webinarId} does not exist.");
                }

                if (!DB::table('users')->where('id', $creatorUserId)->exists()) {
                    return back()->with('error', "User ID {$creatorUserId} does not exist.");
                }

                // Validate rate (should be between 1-5)
                if ($rate < 1 || $rate > 5) {
                    return back()->with('error', "Invalid rating value '{$rate}'. Rating must be between 1 and 5.");
                }

                // Validate status (allow only "pending" or "active")
                if (!in_array($status, ['pending', 'active'])) {
                    $status = 'pending';
                }

                WebinarReview::create([
                    'webinar_id' => $webinarId,
                    'creator_id' => $creatorUserId,
                    'type' => $type,
                    'description' => $comment,
                    'rates' => (string) $rate,
                    'created_at' => now()->timestamp, // Convert to UNIX timestamp
                    'status' => $status,
                ]);
            }

            DB::commit();
            return back()->with('success', 'Reviews imported successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error inserting data: ' . $e->getMessage());
        }
    } catch (\Exception $e) {
        return back()->with('error', 'Error processing file: ' . $e->getMessage());
    }
    }



        // Function to download the Excel template
    public function downloadTemplate()
        {
            $headers = ['Title', 'Student', 'Type', 'Comment', 'Rating (5)', 'Status'];

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers in the first row (A1:H1)
            foreach ($headers as $index => $header) {
                $sheet->setCellValueByColumnAndRow($index + 1, 1, $header);
            }

            // Apply bold styling to headers
            $styleArray = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('A1:F1')->applyFromArray($styleArray);

            // Auto-size columns
            foreach (range('A', 'F') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $fileName = 'webinar_review_template.xlsx';
            $tempFilePath = storage_path('app/' . $fileName);
            $writer->save($tempFilePath);

            return response()->download($tempFilePath)->deleteFileAfterSend(true);

        }
}
