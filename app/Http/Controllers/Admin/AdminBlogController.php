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
use Spatie\ResponseCache\Facades\ResponseCache;

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
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tags' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']).random_int(100, 999);
        $validated['is_published'] = $request->boolean('is_published');
        if ($validated['is_published']) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('thumbnail')) {
            try {
                $validated['thumbnail'] = $this->uploadThumbnail($request->file('thumbnail'));
            } catch (\Throwable $e) {
                return back()->withInput()->with('error', 'Blog thumbnail could not be uploaded. Please check storage permissions.');
            }
        }

        BlogPost::create($validated);

        ResponseCache::clear();

        return redirect()->route('admin.blog.index')->with('success', 'Postingan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $post = BlogPost::findOrFail($id);
        $categories = BlogCategory::all();

        return view('admin.blog.edit', compact('post', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $post = BlogPost::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
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
                $this->deleteStoredFile($post->thumbnail);
            }
            try {
                $validated['thumbnail'] = $this->uploadThumbnail($request->file('thumbnail'));
            } catch (\Throwable $e) {
                return back()->withInput()->with('error', 'Blog thumbnail could not be replaced. Please check storage permissions.');
            }
        }

        $post->update($validated);

        ResponseCache::clear();

        return redirect()->route('admin.blog.index')->with('success', 'Postingan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $post = BlogPost::findOrFail($id);

        if ($post->thumbnail) {
            $this->deleteStoredFile($post->thumbnail);
        }
        $post->delete();
        ResponseCache::clear();

        return redirect()->route('admin.blog.index')->with('success', 'Postingan berhasil dihapus.');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        BlogCategory::create($validated);

        ResponseCache::clear();

        return redirect()->route('admin.blog.index')->with('success', 'Kategori blog berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = BlogCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name,'.$category->id,
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $category->update($validated);

        ResponseCache::clear();

        return redirect()->route('admin.blog.index')->with('success', 'Kategori blog berhasil diperbarui.');
    }

    public function destroyCategory($id)
    {
        $category = BlogCategory::findOrFail($id);
        $category->delete();
        ResponseCache::clear();

        return redirect()->route('admin.blog.index')->with('success', 'Kategori blog berhasil dihapus.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $file = $request->file('upload');
        $filename = time().'_'.uniqid().'_blog.jpg';
        $path = 'uploads/blog/'.$filename;

            try {
                $manager = new ImageManager(new Driver);
                $img = $manager->read($file);
                $img->resizeDown(1600);
                $encoded = $img->toJpeg(92)->toString();
            } catch (\Throwable $e) {
                $ext = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = time().'_'.uniqid().'_blog.'.$ext;
                $path = 'uploads/blog/'.$filename;
                $encoded = file_get_contents($file->getRealPath());
            }

            try {
                if (! Storage::disk('public')->put($path, $encoded)) {
                    throw new \RuntimeException('Unable to write blog image to the public storage disk.');
                }
            } catch (\Throwable $e) {
                report($e);

                return response()->json(['error' => 'Image upload failed. Please check storage permissions.'], 500);
            }

            try {
                $publicDir = public_path('storage/uploads/blog');
                if (! is_dir($publicDir) && ! @mkdir($publicDir, 0755, true) && ! is_dir($publicDir)) {
                    throw new \RuntimeException('Unable to create the blog upload directory.');
                }
                if (@file_put_contents($publicDir.'/'.$filename, $encoded) === false) {
                    throw new \RuntimeException('Unable to publish the blog image.');
                }
            } catch (\Throwable $e) {
                report($e);

                return response()->json(['error' => 'Image could not be published. Please check storage permissions.'], 500);
            }

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    private function uploadThumbnail($file)
    {
        $filename = time().'_'.uniqid().'_thumb.jpg';
        $path = 'uploads/blog/thumbnails/'.$filename;

        try {
            $manager = new ImageManager(new Driver);
            $img = $manager->read($file);
            $img->resizeDown(1200);
            $encoded = $img->toJpeg(90)->toString();
        } catch (\Throwable $e) {
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = time().'_'.uniqid().'_thumb.'.$ext;
            $path = 'uploads/blog/thumbnails/'.$filename;
            $encoded = file_get_contents($file->getRealPath());
        }

        if (! Storage::disk('public')->put($path, $encoded)) {
            throw new \RuntimeException('Unable to write blog thumbnail to the public storage disk.');
        }

        try {
            $dir1 = public_path('storage/uploads/blog/thumbnails');
            if (! is_dir($dir1) && ! @mkdir($dir1, 0777, true) && ! is_dir($dir1)) {
                throw new \RuntimeException('Unable to create the blog thumbnail directory.');
            }
            if (@file_put_contents($dir1.'/'.$filename, $encoded) === false) {
                throw new \RuntimeException('Unable to publish the blog thumbnail.');
            }

            $dir2 = public_path('uploads/blog/thumbnails');
            if (! is_dir($dir2) && ! @mkdir($dir2, 0777, true) && ! is_dir($dir2)) {
                throw new \RuntimeException('Unable to create the blog public thumbnail directory.');
            }
            if (@file_put_contents($dir2.'/'.$filename, $encoded) === false) {
                throw new \RuntimeException('Unable to publish the blog public thumbnail.');
            }
        } catch (\Throwable $e) {
            throw $e;
        }

        return $path;
    }

    private function deleteStoredFile(?string $path): void
    {
        if (! $path || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        $path = ltrim($path, '/');
        Storage::disk('public')->delete($path);

        foreach ([public_path('storage/'.$path), public_path($path)] as $filePath) {
            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }
    }
}
