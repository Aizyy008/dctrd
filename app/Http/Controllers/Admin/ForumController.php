<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ForumsExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Forum;
use App\Models\ForumTopic;
use App\Models\ForumTopicPost;
use App\Models\Group;
use App\Models\Role;
use App\Models\Translation\CategoryTranslation;
use App\Models\Translation\ForumTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        removeContentLocale();

        $this->authorize('admin_forum_list');

        $subForums = $request->get('subForums');

        $forums = Forum::where(function ($query) use ($subForums) {
            if (!empty($subForums)) {
                $query->where('parent_id', $subForums);
            } else {
                $query->whereNull('parent_id');
            }
        })
            ->with([
                'subForums' => function ($query) {
                    $query->where('status', 'active');
                },
            ])
            ->orderBy('id', 'desc')
            ->paginate(10);

        foreach ($forums as $forum) {
            $forumIds = Forum::where('parent_id', $forum->id)->pluck('id')->toArray();
            $forumIds[] = $forum->id;

            $topicsIds = ForumTopic::whereIn('forum_id', $forumIds)->pluck('id')->toArray();

            $forum->topics_count = count($topicsIds);
            $forum->posts_count = ForumTopicPost::whereIn('topic_id', $topicsIds)->count();
        }

        $totalForums = Forum::query()->whereDoesntHave('subForums')->count();
        $totalTopics = ForumTopic::query()->count();
        $postsCount = ForumTopicPost::query()->count();
        $membersCount = ForumTopicPost::select(DB::raw('count(distinct user_id) as count'))->first()->count;

        $data = [
            'pageTitle' => trans('update.forums'),
            'forums' => $forums,
            'totalForums' => $totalForums,
            'totalTopics' => $totalTopics,
            'postsCount' => $postsCount,
            'membersCount' => $membersCount,
        ];

        return view('admin.forums.lists', $data);
    }

    public function create()
    {
        $this->authorize('admin_forum_create');

        $userGroups = Group::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        $roles = Role::all();

        $data = [
            'pageTitle' => trans('update.new_forum'),
            'userGroups' => $userGroups,
            'roles' => $roles
        ];

        return view('admin.forums.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_forum_create');

        $this->validate($request, [
            'title' => 'required|min:3|max:255',
            'description' => 'required',
            'icon' => 'required',
            'role_id' => 'nullable|exists:roles,id',
            'group_id' => 'nullable|exists:groups,id',
            'status' => 'in:active,disabled',
        ]);
        $data = $request->all();

        $forum = Forum::create([
            'slug' => Forum::makeSlug($data['title']),
            'icon' => $data['icon'],
            'group_id' => $data['group_id'] ?? null,
            'role_id' => $data['role_id'] ?? null,
            'status' => $data['status'],
            'close' => (!empty($data['close']) and $data['close'] == 1),
        ]);

        ForumTranslation::updateOrCreate([
            'forum_id' => $forum->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        $hasSubForum = (!empty($request->get('has_sub')) and $request->get('has_sub') == 'on');
        $this->setSubForum($forum, $request->get('sub_forums'), $hasSubForum, $data['locale']);

        removeContentLocale();

        return redirect(getAdminPanelUrl().'/forums');
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('admin_forum_edit');

        $forum = Forum::findOrFail($id);
        $subForums = Forum::where('parent_id', $forum->id)
            ->orderBy('order', 'asc')
            ->get();

        $userGroups = Group::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        $roles = Role::all();

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $forum->getTable(), $forum->id);

        $data = [
            'pageTitle' => trans('admin/main.edit'),
            'forum' => $forum,
            'subForums' => $subForums,
            'userGroups' => $userGroups,
            'roles' => $roles
        ];

        return view('admin.forums.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_forum_edit');

        $this->validate($request, [
            'title' => 'required|min:3|max:255',
            'description' => 'required',
            'icon' => 'required',
            'group_id' => 'nullable|exists:groups,id',
            'role_id' => 'nullable|exists:roles,id',
            'status' => 'in:active,disabled',
        ]);

        $data = $request->all();

        $forum = Forum::findOrFail($id);
        $forum->update([
            'icon' => $data['icon'],
            'group_id' => $data['group_id'] ?? null,
            'role_id' => $data['role_id'] ?? null,
            'status' => $data['status'],
            'close' => (!empty($data['close']) and $data['close'] == 1),
        ]);

        ForumTranslation::updateOrCreate([
            'forum_id' => $forum->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        $hasSubForums = (!empty($request->get('has_sub')) and $request->get('has_sub') == 'on');
        $this->setSubForum($forum, $request->get('sub_forums'), $hasSubForums, $data['locale']);

        removeContentLocale();

        return redirect(getAdminPanelUrl().'/forums');
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_forum_delete');

        $forum = Forum::where('id', $id)->first();

        if (!empty($forum)) {
            Forum::where('parent_id', $forum->id)
                ->delete();

            $forum->delete();
        }

        return redirect(getAdminPanelUrl().'/forums');
    }

    public function search(Request $request)
    {
        $term = $request->get('term');

        $option = $request->get('option', null);

        $query = Forum::select('id')
            ->whereTranslationLike('title', "%$term%");

        /*if (!empty($option)) {

        }*/

        $forums = $query->get();

        $result = [];
        foreach ($forums as $forum) {
            $result[] = [
                'id' => $forum->id,
                'title' => $forum->title,
            ];
        }

        return response()->json($result, 200);
    }

    public function searchTopics(Request $request)
    {
        $term = $request->get('term');

        $option = $request->get('option', null);

        $query = ForumTopic::select('id', 'title')
            ->where('title', 'like', "%$term%");

        $topics = $query->get();

        return response()->json($topics, 200);
    }

    public function setSubForum(Forum $forum, $subForums, $hasSubForums, $locale)
    {
        $order = 1;
        $oldIds = [];

        if ($hasSubForums and !empty($subForums) and count($subForums)) {

            foreach ($subForums as $key => $subForum) {
                $check = Forum::where('id', $key)->first();

                if (is_numeric($key)) {
                    $oldIds[] = $key;
                }

                if (!empty($subForum['title'])) {
                    if (!empty($check)) {
                        $check->update([
                            'order' => $order,
                            'icon' => $subForum['icon'],
                            'group_id' => $subForum['group_id'] ?? null,
                            'role_id' => $subForum['role_id'] ?? null,
                            'status' => $subForum['status'],
                            'close' => $forum->close || ((!empty($subForum['close']) and $subForum['close'] == 1)),
                        ]);

                        ForumTranslation::updateOrCreate([
                            'forum_id' => $check->id,
                            'locale' => mb_strtolower($locale),
                        ], [
                            'title' => $subForum['title'],
                            'description' => $subForum['description'],
                        ]);
                    } else {
                        $new = Forum::create([
                            'slug' => Forum::makeSlug($subForum['title']),
                            'parent_id' => $forum->id,
                            'order' => $order,
                            'icon' => $subForum['icon'],
                            'group_id' => $subForum['group_id'] ?? null,
                            'role_id' => $subForum['role_id'] ?? null,
                            'status' => $subForum['status'],
                            'close' => $forum->close || ((!empty($subForum['close']) and $subForum['close'] == 1)),
                        ]);

                        ForumTranslation::updateOrCreate([
                            'forum_id' => $new->id,
                            'locale' => mb_strtolower($locale),
                        ], [
                            'title' => $subForum['title'],
                            'description' => $subForum['description'],
                        ]);

                        $oldIds[] = $new->id;
                    }

                    $order += 1;
                }
            }
        }

        Forum::where('parent_id', $forum->id)
            ->whereNotIn('id', $oldIds)
            ->delete();

        return true;
    }

    public function export()
    {
        return Excel::download(new ForumsExport, 'forums.xlsx');
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
                if (count($row) < 5) { // Ensure all required columns exist
                    continue;
                }

                $title = trim($row[0]); // Forum Title
                $description = trim($row[1]); // Forum Description
                $iconUrl = trim($row[2]); // Forum Icon URL
                $roleID = !empty(trim($row[3])) ? intval($row[3]) : null;
                $groupID = !empty(trim($row[4])) ? intval($row[4]) : null;
                $status = strtolower(trim($row[5])); // Status (active/disabled)
                $closed = intval($row[6]); // Closed (1 for closed, 0 for open)

                // Validate status (only allow "active" or "disabled")
                if (!in_array($status, ['active', 'disabled'])) {
                    $status = 'disabled';
                }

                // Validate closed (only allow 0 or 1)
                if (!in_array($closed, [0, 1])) {
                    $closed = 0;
                }

                // Create Forum Entry
                $forum = Forum::create([
                    'slug' => $this->generateUniqueSlug($title),
                    'parent_id' => null, // Default as null (main forum)
                    'role_id' => $roleID,
                    'group_id' => $groupID,
                    'icon' => $iconUrl,
                    'status' => $status,
                    'close' => $closed,
                    'created_at' => now()->timestamp,
                ]);

                // Insert into forum_translations table
                DB::table('forum_translations')->insert([
                    [
                        'forum_id' => $forum->id,
                        'locale' => 'en',
                        'title' => $title,
                        'description' => $description,
                    ]
                ]);
            }

            DB::commit();
            return back()->with('success', 'Forums imported successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error inserting data: ' . $e->getMessage());
        }
    } catch (\Exception $e) {
        return back()->with('error', 'Error processing file: ' . $e->getMessage());
    }
}
    private function generateUniqueSlug($title)
    {
        $slug = Str()->slug($title);
        $count = 1;

        while (Forum::where('slug', $slug)->exists()) {
            $slug = Str()->slug($title) . '-' . $count;
            $count++;
        }

        return $slug;
    }



    public function downloadTemplate()
{
    $headers = [
        'Title', // Forum title in different languages
        'Description',
        'Icon URL', // Forum icon
        'Role ID', // Forum icon
        'Group ID', // Forum icon
        'Status', // active or disabled
        'Closed' // 1 for closed, 0 for open
    ];

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set headers in the first row (A1:I1)
    foreach ($headers as $index => $header) {
        $sheet->setCellValueByColumnAndRow($index + 1, 1, $header);
    }

    // Apply bold styling to headers
    $styleArray = [
        'font' => ['bold' => true],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
    ];
    $sheet->getStyle('A1:G1')->applyFromArray($styleArray);

    // Auto-size columns
    foreach (range('A', 'G') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    $writer = new Xlsx($spreadsheet);
    $fileName = 'forum_import_template.xlsx';
    $tempFilePath = storage_path('app/' . $fileName);
    $writer->save($tempFilePath);

    return response()->download($tempFilePath)->deleteFileAfterSend(true);
}


}
