<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AdminBlogController extends Controller
{
    public function index()
    {
        $posts = new LengthAwarePaginator(collect(), 0, 15, 1);
        $categories = collect();

        try {
            if (Schema::hasTable('blog_posts')) {
                $posts = BlogPost::with('category')->latest()->paginate(15);
            }
        } catch (QueryException $e) {
            // Table might not exist yet
        }

        try {
            if (Schema::hasTable('blog_categories')) {
                $categories = BlogCategory::all();
            }
        } catch (QueryException $e) {
            // Table might not exist yet
        }

        return view('admin.blog.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = collect();

        try {
            if (Schema::hasTable('blog_categories')) {
                $categories = BlogCategory::all();
            }
        } catch (QueryException $e) {
            // Table might not exist yet
        }

        return view('admin.blog.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tags' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']).random_int(100, 999);
        $validated['is_published'] = $request->boolean('is_published');
        if ($validated['is_published']) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $this->uploadThumbnail($request->file('thumbnail'));
        }

        BlogPost::create($validated);

        return redirect()->route('admin.blog.index')->with('success', 'Postingan berhasil ditambahkan.');
    }

    public function edit(BlogPost $post)
    {
        $categories = BlogCategory::all();

        return view('admin.blog.edit', compact('post', 'categories'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'tags' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        if ($request->input('title') !== $post->title) {
            $validated['slug'] = Str::slug($validated['title']).random_int(100, 999);
        }

        if ($validated['is_published'] && ! $post->is_published) {
            $validated['published_at'] = now();
        } elseif (! $validated['is_published']) {
            $validated['published_at'] = null;
        }

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $validated['thumbnail'] = $this->uploadThumbnail($request->file('thumbnail'));
        }

        $post->update($validated);

        return redirect()->route('admin.blog.index')->with('success', 'Postingan berhasil diperbarui.');
    }

    public function destroy(BlogPost $post)
    {
        if ($post->thumbnail) {
            Storage::disk('public')->delete($post->thumbnail);
        }
        $post->delete();

        return redirect()->route('admin.blog.index')->with('success', 'Postingan berhasil dihapus.');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        BlogCategory::create($validated);

        return redirect()->route('admin.blog.index')->with('success', 'Kategori blog berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, BlogCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name,'.$category->id,
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $category->update($validated);

        return redirect()->route('admin.blog.index')->with('success', 'Kategori blog berhasil diperbarui.');
    }

    public function destroyCategory(BlogCategory $category)
    {
        $category->delete();

        return redirect()->route('admin.blog.index')->with('success', 'Kategori blog berhasil dihapus.');
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $filename = time().'_blog.webp';
            $path = 'uploads/blog/'.$filename;

            $manager = new ImageManager(new Driver);
            $img = $manager->read($file);
            $img->resizeDown(1200);
            $encoded = $img->toWebp(80)->toString();

            Storage::disk('public')->put($path, $encoded);

            return response()->json([
                'url' => Storage::disk('public')->url($path),
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    private function uploadThumbnail($file)
    {
        $filename = time().'_thumb.webp';
        $path = 'uploads/blog/thumbnails/'.$filename;

        $manager = new ImageManager(new Driver);
        $img = $manager->read($file);
        $img->resizeDown(800);
        $encoded = $img->toWebp(80)->toString();

        Storage::disk('public')->put($path, $encoded);

        return $path;
    }
}
