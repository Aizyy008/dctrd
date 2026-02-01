<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ForumFeaturedTopicExport;
use App\Http\Controllers\Controller;
use App\Models\ForumFeaturedTopic;
use App\Models\ForumTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FeaturedTopicsController extends Controller
{
    public function index()
    {
        $this->authorize('admin_featured_topics_list');

        $featuredTopics = ForumFeaturedTopic::orderBy('created_at', 'desc')
            ->with([
                'topic'
            ])
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.featured_topics'),
            'featuredTopics' => $featuredTopics
        ];

        return view('admin.forums.featured_topics.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_featured_topics_create');

        $data = [
            'pageTitle' => trans('update.new_featured_topic'),
        ];

        return view('admin.forums.featured_topics.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_featured_topics_create');

        $this->validate($request, [
            'topic_id' => 'required|exists:forum_topics,id',
            'icon' => 'required'
        ]);

        $data = $request->all();

        ForumFeaturedTopic::create([
            'topic_id' => $data['topic_id'],
            'icon' => $data['icon'],
            'created_at' => time()
        ]);

        return redirect(getAdminPanelUrl().'/featured-topics');
    }

    public function edit($id)
    {
        $this->authorize('admin_featured_topics_edit');

        $feature = ForumFeaturedTopic::where('id', $id)
            ->with([
                'topic'
            ])
            ->first();

        if (!empty($feature)) {
            $data = [
                'pageTitle' => trans('update.edit_featured_topic'),
                'feature' => $feature
            ];

            return view('admin.forums.featured_topics.create', $data);
        }

        abort(404);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_featured_topics_edit');

        $this->validate($request, [
            'topic_id' => 'required|exists:forum_topics,id',
            'icon' => 'required'
        ]);

        $feature = ForumFeaturedTopic::findOrFail($id);

        $data = $request->all();

        $feature->update([
            'topic_id' => $data['topic_id'],
            'icon' => $data['icon'],
        ]);

        return redirect(getAdminPanelUrl().'/featured-topics');
    }

    public function destroy($id)
    {
        $this->authorize('admin_featured_topics_delete');

        $feature = ForumFeaturedTopic::findOrFail($id);

        $feature->delete();

        return back();
    }
    public function export()
    {
        return Excel::download(new ForumFeaturedTopicExport, 'Forum_Featured_Topic.xlsx');
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
                    if (count($row) < 2) { // Ensure all required columns exist (topic_id & icon)
                        continue;
                    }

                    $iconUrl = trim($row[0]); // Get icon URL from Excel
                    $topicId = intval($row[1]); // Get topic_id from Excel

                    // Insert into forum_featured_topics table
                    DB::table('forum_featured_topics')->insert([
                        'topic_id' => $topicId,
                        'icon' => $iconUrl,
                        'created_at' => now()->timestamp, // Convert to Unix timestamp
                ]);
                }

                DB::commit();
                return back()->with('success', 'Featured topics imported successfully!');
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
    $headers = [
        'Icon URL',     // The icon URL
        'Topic ID',  // The title of the forum topic
    ];

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set headers in the first row (A1:C1)
    foreach ($headers as $index => $header) {
        $sheet->setCellValueByColumnAndRow($index + 1, 1, $header);
    }

    // Apply bold styling to headers
    $styleArray = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    ];
    $sheet->getStyle('A1:B1')->applyFromArray($styleArray);

    // Auto-size columns
    foreach (range('A', 'B') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    // Save and return the file
    $writer = new Xlsx($spreadsheet);
    $fileName = 'forum_featured_topics_template.xlsx';
    $tempFilePath = storage_path('app/' . $fileName);
    $writer->save($tempFilePath);

    return response()->download($tempFilePath)->deleteFileAfterSend(true);
}


}
