<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminMenu;
use App\Models\Article;
use App\Models\ArticleType;
use App\Models\SectorDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Exception;

class ArticleController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $articles = Article::with(['type', 'sector'])->latest()->get();

        return view('admin.articles.index', compact('articles', 'menus'));
    }

    public function create()
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $types = ArticleType::where('status', 1)->get();
        $sections = SectorDetail::where('status', 1)->get();
        $allTags=Article::select('keywords')->distinct()->get();

        return view('admin.articles.form', compact('allTags','types', 'sections', 'menus'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'articletype_id' => 'nullable|exists:article_types,id',
                'sector_details_id' => 'nullable|exists:sector_details,id',
                'entitle' => 'required|string|max:255',
                'maltitle' => 'nullable|string|max:255',
                'endescription' => 'nullable|string',
                'maldescription' => 'nullable|string',
                'encontent' => 'nullable|string',
                'malcontent' => 'nullable|string',
                'poster' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10048',
                'banner' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10048',
                'status' => 'boolean',
            ]);

            if (!empty($validated['articletype_id'])) {
                $validated['articletype'] = \DB::table('article_types')
                    ->where('id', $validated['articletype_id'])
                    ->value('entitle');
            }

             $rawKeywords = $request->input('keywords');

            /*
            | Convert array → string if needed
            */
            if (is_array($rawKeywords)) {
                $rawKeywords = implode(',', $rawKeywords);
            }

            $cleanKeywords = collect(explode(',', $rawKeywords))
                ->map(fn ($k) => trim($k))
                ->filter(fn ($k) => $k !== '')
                ->unique()
                ->values()
                ->toArray();

            $validated['keywords'] = !empty($cleanKeywords)
                ? json_encode($cleanKeywords, JSON_UNESCAPED_UNICODE)
                : null;

            // ✅ Handle Poster Upload
            if ($request->hasFile('poster')) {
                $posterFile = $request->file('poster');
                $posterPath = public_path('articles/poster');

                // Create folder if it doesn't exist
                if (!File::exists($posterPath)) {
                    File::makeDirectory($posterPath, 0755, true);
                }

                $posterName = time() . '_poster_' . $posterFile->getClientOriginalName();
                $posterFile->move($posterPath, $posterName);
                $validated['poster'] = 'articles/poster/' . $posterName;
            }

            // ✅ Handle Banner Upload
            if ($request->hasFile('banner')) {
                $bannerFile = $request->file('banner');
                $bannerPath = public_path('articles/banner');

                if (!File::exists($bannerPath)) {
                    File::makeDirectory($bannerPath, 0755, true);
                }

                $bannerName = time() . '_banner_' . $bannerFile->getClientOriginalName();
                $bannerFile->move($bannerPath, $bannerName);
                $validated['banner'] = 'articles/banner/' . $bannerName;
            }

            Article::create($validated);

            return redirect()->route('admin.articles.index')->with('success', 'Article added successfully.');
        } catch (Exception $e) {
            Log::error('Error adding article: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to add article.');
        }
    }

    public function edit(Article $article)
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $types = ArticleType::where('status', 1)->get();
        $sections = SectorDetail::where('status', 1)->get();
        $allTags=Article::select('keywords')->distinct()->get();

        return view('admin.articles.form', compact('allTags','article', 'types', 'sections', 'menus'));
    }

    public function update(Request $request, Article $article)
    {
        try {
            $validated = $request->validate([
                'articletype_id' => 'nullable|exists:article_types,id',
                'sector_details_id' => 'nullable|exists:sector_details,id',
                'entitle' => 'required|string|max:255',
                'maltitle' => 'nullable|string|max:255',
                'endescription' => 'nullable|string',
                'maldescription' => 'nullable|string',
                'encontent' => 'nullable|string',
                'malcontent' => 'nullable|string',
                'poster' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10048',
                'banner' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:10048',
                'status' => 'boolean',
            ]);


            // dd($validated);
            // ✅ Poster Upload
            // dd($validated['articletype_id']);
            if (!empty($validated['articletype_id'])) {
                $validated['articletype'] = \DB::table('article_types')
                    ->where('id', $validated['articletype_id'])
                    ->value('entitle');
            }


            $rawKeywords = $request->input('keywords');

            /*
            | Convert array → string if needed
            */
            if (is_array($rawKeywords)) {
                $rawKeywords = implode(',', $rawKeywords);
            }

            $cleanKeywords = collect(explode(',', $rawKeywords))
                ->map(fn ($k) => trim($k))
                ->filter(fn ($k) => $k !== '')
                ->unique()
                ->values()
                ->toArray();

            $validated['keywords'] = !empty($cleanKeywords)
                ? json_encode($cleanKeywords, JSON_UNESCAPED_UNICODE)
                : null;




            if ($request->hasFile('poster')) {
                $posterFile = $request->file('poster');
                $posterPath = public_path('articles/poster');

                if (!File::exists($posterPath)) {
                    File::makeDirectory($posterPath, 0755, true);
                }

                // Delete old file if exists
                if ($article->poster && File::exists(public_path($article->poster))) {
                    File::delete(public_path($article->poster));
                }

                $posterName = time() . '_poster_' . $posterFile->getClientOriginalName();
                $posterFile->move($posterPath, $posterName);
                $validated['poster'] = 'articles/poster/' . $posterName;
            }

            // ✅ Banner Upload
            if ($request->hasFile('banner')) {
                $bannerFile = $request->file('banner');
                $bannerPath = public_path('articles/banner');

                if (!File::exists($bannerPath)) {
                    File::makeDirectory($bannerPath, 0755, true);
                }

                if ($article->banner && File::exists(public_path($article->banner))) {
                    File::delete(public_path($article->banner));
                }

                $bannerName = time() . '_banner_' . $bannerFile->getClientOriginalName();
                $bannerFile->move($bannerPath, $bannerName);
                $validated['banner'] = 'articles/banner/' . $bannerName;
            }

            $res=$article->update($validated);
            // dd($res);

            return redirect()->route('admin.articles.index')->with('success', 'Article updated successfully.');
        } catch (Exception $e) {
            Log::error('Error updating article: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update article.');
        }
    }

    public function destroy(Article $article)
    {
        try {
            // ✅ Delete images from storage
            if ($article->poster && File::exists(public_path($article->poster))) {
                File::delete(public_path($article->poster));
            }

            if ($article->banner && File::exists(public_path($article->banner))) {
                File::delete(public_path($article->banner));
            }

            $article->delete();

            return redirect()->route('admin.articles.index')->with('success', 'Article deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting article: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete article.');
        }
    }
}
