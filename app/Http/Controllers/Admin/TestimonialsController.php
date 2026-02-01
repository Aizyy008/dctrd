<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TestimonialsExport;
use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\Translation\TestimonialTranslation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TestimonialsController extends Controller
{
    public function index()
    {
        $this->authorize('admin_testimonials_list');

        removeContentLocale();

        $testimonials = Testimonial::query()->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/comments.testimonials'),
            'testimonials' => $testimonials
        ];

        return view('admin.testimonials.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_testimonials_create');

        removeContentLocale();

        $data = [
            'pageTitle' => trans('admin/pages/comments.new_testimonial'),
        ];

        return view('admin.testimonials.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_testimonials_create');

        $this->validate($request, [
            'user_avatar' => 'required|string',
            'user_name' => 'required|string',
            'user_bio' => 'required|string',
            'rate' => 'required|integer|between:0,5',
            'comment' => 'required|string',
        ]);

        $data = $request->all();

        $testimonial = Testimonial::create([
            'user_avatar' => $data['user_avatar'],
            'rate' => $data['rate'],
            'status' => $data['status'],
            'created_at' => time(),
        ]);

        if (!empty($testimonial)) {
            TestimonialTranslation::updateOrCreate([
                'testimonial_id' => $testimonial->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'user_name' => $data['user_name'],
                'user_bio' => $data['user_bio'],
                'comment' => $data['comment'],
            ]);
        }

        return redirect(getAdminPanelUrl().'/testimonials');
    }


    public function edit(Request $request, $id)
    {
        $this->authorize('admin_testimonials_edit');

        $testimonial = Testimonial::findOrFail($id);

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $testimonial->getTable(), $testimonial->id);

        $data = [
            'pageTitle' => trans('admin/pages/comments.edit_testimonial'),
            'testimonial' => $testimonial
        ];

        return view('admin.testimonials.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_testimonials_edit');

        $this->validate($request, [
            'user_avatar' => 'required|string',
            'user_name' => 'required|string',
            'user_bio' => 'required|string',
            'rate' => 'required|integer|between:0,5',
            'comment' => 'required|string',
        ]);

        $testimonial = Testimonial::findOrFail($id);

        $data = $request->all();

        $testimonial->update([
            'user_avatar' => $data['user_avatar'],
            'rate' => $data['rate'],
            'status' => $data['status'],
        ]);

        TestimonialTranslation::updateOrCreate([
            'testimonial_id' => $testimonial->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'user_name' => $data['user_name'],
            'user_bio' => $data['user_bio'],
            'comment' => $data['comment'],
        ]);

        removeContentLocale();

        return redirect(getAdminPanelUrl().'/testimonials');
    }

    public function delete($id)
    {
        $this->authorize('admin_testimonials_delete');

        $testimonial = Testimonial::findOrFail($id);

        $testimonial->delete();

        return redirect(getAdminPanelUrl().'/testimonials');
    }
        public function downloadTemplate()
    {
        // Define the headers for the Excel sheet
        $headers = [
            'User Avatar', 'User Name', 'Job Title', 'Rate', 'Comment', 'Status'
        ];

        // Create a new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers in the first row
        $columnIndex = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($columnIndex . '1', $header);
            $columnIndex++;
        }

        // Set header styling (Bold)
        $styleArray = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($styleArray);

        // Auto-size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Generate Excel file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'testimonials_template.xlsx';
        $tempFilePath = storage_path('app/' . $fileName);
        $writer->save($tempFilePath);

        // Return the file as a response for download
        return response()->download($tempFilePath)->deleteFileAfterSend(true);
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
                return back()->with('error', 'The uploaded file is empty or has incorrect format.');
            }

            // Remove the header row
            array_shift($data);

            foreach ($data as $row) {
                // Ensure all required fields exist
                if (count($row) < 6) {
                    continue;
                }

                 // Validate rate column (should be between 0 and 5)
                $validator = Validator::make(['rate' => $row[3]], [
                    'rate' => 'required|numeric|min:0|max:5',
                ]);

                if ($validator->fails()) {
                    return back()->withErrors(['rate' => 'The rate must be between 0 and 5. Found: ' . $row[3]]);
                }

                // Store in testimonials table
                $testimonial = Testimonial::create([
                    'user_avatar' => $row[0] ?? null, // User Avatar
                    'rate' => $row[3] ?? 0, // Rate
                    'status' => $row[5] ?? 'disable', // Status
                    'created_at' => now()->timestamp, // Store current timestamp
                ]);

                // Store in testimonial_translations table
                if ($testimonial) {
                    TestimonialTranslation::create([
                        'testimonial_id' => $testimonial->id,
                        'locale' => app()->getLocale(),
                        'user_name' => $row[1] ?? '', // User Name
                        'user_bio' => $row[2] ?? '', // Job Title (Bio)
                        'comment' => $row[4] ?? '', // Comment
                    ]);
                }
            }

            return back()->with('success', 'Testimonials imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing testimonials: ' . $e->getMessage());
        }
    }
        public function export()
    {
        return Excel::download(new TestimonialsExport, 'testimonials.xlsx');
    }


}
