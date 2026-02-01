<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BlogsExport;
use App\Http\Controllers\Admin\traits\ProductBadgeTrait;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Reward;
use App\Models\RewardAccounting;
use App\Models\Translation\BlogTranslation;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BlogController extends Controller
{
    use ProductBadgeTrait;

    public function index(Request $request)
    {
        removeContentLocale();

        $this->authorize('admin_blog_lists');

        $query = Blog::query();

        $blog = $this->filters($query, $request)
            ->with(['category', 'author' => function ($query) {
                $query->select('id', 'full_name');
            }])
            ->withCount('comments')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $blogCategories = BlogCategory::all();
        $adminRoleIds = Role::where('is_admin', true)->pluck('id')->toArray();
        $authors = User::select('id', 'full_name', 'role_id')->whereIn('role_id', $adminRoleIds)->get();

        $data = [
            'pageTitle' => trans('admin/pages/blog.blog'),
            'blog' => $blog,
            'blogCategories' => $blogCategories,
            'authors' => $authors,
        ];

        return view('admin.blog.lists', $data);
    }

    private function filters($query, $request)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $title = $request->get('title', null);
        $category_id = $request->get('category_id', null);
        $author_id = $request->get('author_id', null);
        $status = $request->get('status', null);

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');


        if (!empty($title)) {
            $query->whereTranslationLike('title', '%' . $title . '%');
        }

        if (!empty($category_id)) {
            $query->where('category_id', $category_id);
        }

        if (!empty($author_id)) {
            $query->where('author_id', $author_id);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        return $query;
    }

    public function create()
    {
        $this->authorize('admin_blog_create');

        $categories = BlogCategory::all();

        $data = [
            'pageTitle' => trans('admin/pages/blog.create_blog'),
            'categories' => $categories
        ];

        return view('admin.blog.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_blog_create');

        $this->validate($request, [
            'locale' => 'required',
            'title' => 'required|string|max:255',
            'category_id' => 'required|numeric',
            'image' => 'required|string',
            'description' => 'required|string',
            'content' => 'required|string',
        ]);

        $data = $request->all();

        $blog = Blog::create([
            'slug' => Blog::makeSlug($data['title']),
            'category_id' => $data['category_id'],
            'author_id' => !empty($data['author_id']) ? $data['author_id'] : auth()->id(),
            'image' => $data['image'],
            'enable_comment' => (!empty($data['enable_comment']) and $data['enable_comment'] == 'on'),
            'status' => (!empty($data['status']) and $data['status'] == 'on') ? 'publish' : 'pending',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        if ($blog) {
            BlogTranslation::updateOrCreate([
                'blog_id' => $blog->id,
                'locale' => mb_strtolower($data['locale']),
            ], [
                'title' => $data['title'],
                'description' => $data['description'],
                'meta_description' => $data['meta_description'],
                'content' => $data['content'],
            ]);

            if ($blog->status == 'publish' and $blog->author_id != auth()->id()) {
                $notifyOptions = [
                    '[blog_title]' => $blog->title,
                ];
                sendNotification('publish_instructor_blog_post', $notifyOptions, $blog->author_id);
            }
        }

        return redirect(getAdminPanelUrl().'/blog');
    }

    public function edit(Request $request, $post_id)
    {
        $this->authorize('admin_blog_edit');

        $post = Blog::findOrFail($post_id);

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $post->getTable(), $post->id);

        $categories = BlogCategory::all();

        $data = [
            'pageTitle' => trans('admin/pages/blog.create_blog'),
            'categories' => $categories,
            'post' => $post,
        ];

        return view('admin.blog.create', $data);
    }

    public function update(Request $request, $post_id)
    {
        $this->authorize('admin_blog_edit');

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'category_id' => 'required|numeric',
            'image' => 'required|string',
            'description' => 'required|string',
            'content' => 'required|string',
        ]);

        $data = $request->all();
        $post = Blog::findOrFail($post_id);

        $post->update([
            'category_id' => $data['category_id'],
            'author_id' => !empty($data['author_id']) ? $data['author_id'] : $post->author_id,
            'image' => $data['image'],
            'enable_comment' => (!empty($data['enable_comment']) and $data['enable_comment'] == 'on'),
            'status' => (!empty($data['status']) and $data['status'] == 'on') ? 'publish' : 'pending',
            'updated_at' => time(),
        ]);


        BlogTranslation::updateOrCreate([
            'blog_id' => $post->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'description' => $data['description'],
            'meta_description' => $data['meta_description'],
            'content' => $data['content'],
        ]);

        // Product Badge
        $this->handleProductBadges($post, $data);

        removeContentLocale();

        if ($post->status == 'publish' and $post->author_id != auth()->id()) {

            $createPostReward = RewardAccounting::calculateScore(Reward::CREATE_BLOG_BY_INSTRUCTOR);
            RewardAccounting::makeRewardAccounting($post->author_id, $createPostReward, Reward::CREATE_BLOG_BY_INSTRUCTOR, $post->id, true);


            $notifyOptions = [
                '[blog_title]' => $post->title,
            ];
            sendNotification('publish_instructor_blog_post', $notifyOptions, $post->author_id);
        }

        return redirect(getAdminPanelUrl().'/blog');
    }

    public function delete($post_id)
    {
        $this->authorize('admin_blog_delete');

        $post = Blog::findOrFail($post_id);

        $post->delete();

        return redirect(getAdminPanelUrl().'/blog');
    }

    public function search(Request $request)
    {
        $term = $request->get('term');
        $posts = Blog::select('id')
            ->whereTranslationLike('title', "%$term%")
            ->get();

        $result = [];
        foreach ($posts as $post) {
            $result[] = [
                'id' => $post->id,
                'title' => $post->title,
            ];
        }

        return response()->json($result, 200);
    }

    public function export()
    {
        return Excel::download(new BlogsExport, 'blogs.xlsx');
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
                if (count($row) < 6) {
                    continue;
                }

                $titleEn = trim($row[0]);
                $titleAr = trim($row[1]);
                $titleEs = trim($row[2]);
                $categoryId = intval($row[3]);
                $authorId = intval($row[4]);
                $status = strtolower(trim($row[5]));

                // Validate category
                $category = DB::table('blog_categories')->where('id', $categoryId)->exists();
                if (!$category) {
                    return back()->with('error', "Category ID '{$categoryId}' not found.");
                }

                // Validate author
                $author = DB::table('users')->where('id', $authorId)->exists();
                if (!$author) {
                    return back()->with('error', "Author ID '{$authorId}' not found.");
                }

                // Validate status (only allow "pending" or "published")
                if (!in_array($status, ['pending', 'publish'])) {
                    $status = 'pending';
                }

                // Create Blog Entry
                $blog = Blog::create([
                    'category_id' => $categoryId,
                    'author_id' => $authorId,
                    'slug' => Str()->slug($titleEn), // Generate a unique slug
                    'status' => $status,
                    'visit_count' => 0,
                    'enable_comment' => 1,
                     'created_at' => now()->timestamp, // Convert to UNIX timestamp
                ]);

                // Insert into blog_translations for multiple languages
                DB::table('blog_translations')->insert([
                    [
                        'blog_id' => $blog->id,
                        'locale' => 'en',
                        'title' => $titleEn,
                        'description' => null,
                        'content' => null,
                        'meta_description' => null,
                    ],
                    [
                        'blog_id' => $blog->id,
                        'locale' => 'ar',
                        'title' => $titleAr,
                        'description' => null,
                        'content' => null,
                        'meta_description' => null,

                    ],
                    [
                        'blog_id' => $blog->id,
                        'locale' => 'es',
                        'title' => $titleEs,
                        'description' => null,
                        'content' => null,
                        'meta_description' => null,
                    ]
                ]);
            }

            DB::commit();
            return back()->with('success', 'Blogs imported successfully!');
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
        $headers = ['Title En','Title Ar','Title Es','Category ID', 'Author ID', 'Status'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers in the first row (A1:F1)
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
        $fileName = 'blog_import_template.xlsx';
        $tempFilePath = storage_path('app/' . $fileName);
        $writer->save($tempFilePath);

        return response()->download($tempFilePath)->deleteFileAfterSend(true);
    }

}
