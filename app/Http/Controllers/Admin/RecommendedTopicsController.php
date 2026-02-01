<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ForumRecommendedTopicExport;
use App\Http\Controllers\Controller;
use App\Models\ForumRecommendedTopic;
use App\Models\ForumRecommendedTopicItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RecommendedTopicsController extends Controller
{
    public function index()
    {
        $this->authorize('admin_recommended_topics_list');

        $recommendedTopics = ForumRecommendedTopic::orderBy('created_at', 'desc')
            ->with([
                'topics'
            ])
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.recommended_topics'),
            'recommendedTopics' => $recommendedTopics
        ];

        return view('admin.forums.recommended_topics.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_recommended_topics_create');

        $data = [
            'pageTitle' => trans('update.new_recommended_topic'),
        ];

        return view('admin.forums.recommended_topics.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_recommended_topics_create');

        $this->validate($request, [
            'topic_ids' => 'required|array|min:1',
            'title' => 'required|max:255',
            'icon' => 'required|max:255',
        ]);

        $data = $request->all();

        $recommended = ForumRecommendedTopic::create([
            'title' => $data['title'],
            'icon' => $data['icon'],
            'created_at' => time()
        ]);

        $this->handleTopicItems($recommended, $data['topic_ids']);

        return redirect(getAdminPanelUrl().'/recommended-topics');
    }

    private function handleTopicItems($recommended, $topicIds)
    {
        ForumRecommendedTopicItem::where('recommended_topic_id', $recommended->id)
            ->delete();

        if (!empty($topicIds)) {
            foreach ($topicIds as $topicId) {
                ForumRecommendedTopicItem::create([
                    'recommended_topic_id' => $recommended->id,
                    'topic_id' => $topicId,
                    'created_at' => time(),
                ]);
            }
        }
    }

    public function edit($id)
    {
        $this->authorize('admin_recommended_topics_edit');

        $recommended = ForumRecommendedTopic::where('id', $id)
            ->with([
                'topics'
            ])
            ->first();

        if (!empty($recommended)) {
            $data = [
                'pageTitle' => trans('update.edit_recommended_topic'),
                'recommended' => $recommended
            ];

            return view('admin.forums.recommended_topics.create', $data);
        }

        abort(404);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_recommended_topics_edit');

        $this->validate($request, [
            'topic_ids' => 'required|array|min:1',
            'title' => 'required|max:255',
            'icon' => 'required|max:255',
        ]);

        $recommended = ForumRecommendedTopic::findOrFail($id);

        $data = $request->all();

        $recommended->update([
            'title' => $data['title'],
            'icon' => $data['icon'],
        ]);

        $this->handleTopicItems($recommended, $data['topic_ids']);

        return redirect(getAdminPanelUrl().'/recommended-topics');
    }

    public function destroy($id)
    {
        $this->authorize('admin_recommended_topics_delete');

        $recommended = ForumRecommendedTopic::findOrFail($id);

        $recommended->delete();

        return back();
    }

    public function export()
    {
        return Excel::download(new ForumRecommendedTopicExport, 'Forum_Recommended_Topic.xlsx');
    }
 public function import(Request $request)
{
    $request->validate([
        'excel_file' => 'required|mimes:xlsx,xls,csv'
    ]);

    if (!$request->hasFile('excel_file')) {
        return redirect()->back()->withErrors(['excel_file' => 'No file was uploaded.']);
    }

    $file = $request->file('excel_file');

    try {
        $spreadsheet = IOFactory::load($file->getPathname());
        $data = $spreadsheet->getActiveSheet()->toArray();

        if (empty($data) || count($data) < 2) {
            return redirect()->back()->withErrors(['excel_file' => 'The uploaded file is empty or has an incorrect format.']);
        }

        // Remove the header row
        array_shift($data);

        $errors = [];
        DB::beginTransaction();
        try {
            foreach ($data as $index => $row) {
                if (count($row) < 3) {
                    $errors[] = "Row " . ($index + 2) . ": Missing required columns.";
                    continue;
                }

                $iconUrl = trim($row[0] ?? '');
                $title = trim($row[1] ?? '');
                $topicId = isset($row[2]) ? intval($row[2]) : null;

                if (empty($iconUrl) || empty($title) || empty($topicId)) {
                    $errors[] = "Row " . ($index + 2) . ": Missing required values.";
                    continue;
                }

                if (!filter_var($iconUrl, FILTER_VALIDATE_URL)) {
                    $errors[] = "Row " . ($index + 2) . ": Invalid Icon URL.";
                    continue;
                }

                $existingTopic = DB::table('forum_recommended_topic_items')->where('topic_id', $topicId)->first();
                if (!$existingTopic) {
                    $errors[] = "Row " . ($index + 2) . ": Invalid Topic ID.";
                    continue;
                }

                $recommendedTopic = DB::table('forum_recommended_topics')->where('title', $title)->first();

                if (!$recommendedTopic) {
                    $recommendedTopicId = DB::table('forum_recommended_topics')->insertGetId([
                        'title' => $title,
                        'icon' => $iconUrl,
                        'created_at' => now()->timestamp, // Convert to Unix timestamp
                    ]);
                } else {
                    $recommendedTopicId = $recommendedTopic->id;
                }

                DB::table('forum_recommended_topic_items')->updateOrInsert(
                    [
                        'recommended_topic_id' => $recommendedTopicId,
                        'topic_id' => $topicId
                    ],
                    [
                        'created_at' => now()->timestamp, // Convert to Unix timestamp
                    ]
                );
            }

            DB::commit();

            if (!empty($errors)) {
                return redirect()->back()->withErrors($errors);
            }

            return redirect()->back()->with('success', 'Recommended topics imported successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error inserting data: ' . $e->getMessage()]);
        }
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['error' => 'Error processing file: ' . $e->getMessage()]);
    }
}


    public function downloadTemplate()
{
    $headers = [
        'Icon URL',        // URL for the topic icon
        'Title',           // Recommended topic title
        'Topic ID',        // Topic ID (from forum_recommended_topic_items)
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
    $sheet->getStyle('A1:C1')->applyFromArray($styleArray);

    // Auto-size columns
    foreach (range('A', 'C') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    // Save the file in storage and return it for download
    $fileName = 'forum_recommended_topics_template.xlsx';
    $filePath = storage_path('app/public/' . $fileName);

    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);

    return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
