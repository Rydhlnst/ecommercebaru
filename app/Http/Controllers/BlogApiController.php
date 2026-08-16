<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class BlogApiController extends Controller
{
    /**
     * List published blog posts (public).
     */
    public function index(Request $request): JsonResponse
    {
        $query = BlogPost::with('category')
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->latest();

        if ($categorySlug = $request->get('category')) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate($request->get('per_page', 9))->withQueryString();

        return response()->json([
            'success' => true,
            'data' => $posts->items(),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    /**
     * Show a single published blog post (public).
     */
    public function show(string $slug): JsonResponse
    {
        $post = BlogPost::with('category')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $recentPosts = BlogPost::with('category')
            ->where('id', '!=', $post->id)
            ->where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->latest()
            ->limit(4)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'slug' => $p->slug,
                'thumbnail' => $p->thumbnail_url,
                'category' => $p->category?->name,
                'published_at' => $p->published_at?->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'content' => $post->content,
                'thumbnail' => $post->thumbnail_url,
                'tags' => $post->tags,
                'category' => [
                    'id' => $post->category?->id,
                    'name' => $post->category?->name,
                    'slug' => $post->category?->slug,
                ],
                'published_at' => $post->published_at?->toIso8601String(),
                'created_at' => $post->created_at?->toIso8601String(),
                'updated_at' => $post->updated_at?->toIso8601String(),
            ],
            'recent_posts' => $recentPosts,
        ]);
    }

    /**
     * List all blog posts including drafts (admin).
     */
    public function adminIndex(Request $request): JsonResponse
    {
        $query = BlogPost::with('category')->latest();

        if ($request->has('published')) {
            $query->where('is_published', $request->boolean('published'));
        }

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('blog_category_id', $categoryId);
        }

        $posts = $query->paginate($request->get('per_page', 15))->withQueryString();

        return response()->json([
            'success' => true,
            'data' => $posts->items(),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    /**
     * Store a new blog post (admin).
     */
    public function store(Request $request): JsonResponse
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
            $validated['thumbnail'] = $this->uploadThumbnail($request->file('thumbnail'));
        } else {
            $validated['thumbnail'] = null;
        }

        $post = BlogPost::create($validated);

        $post->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Postingan berhasil ditambahkan.',
            'data' => $post,
        ], 201);
    }

    /**
     * Update a blog post (admin).
     */
    public function update(Request $request, BlogPost $post): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tags' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        $data = $request->only([
            'title', 'content', 'blog_category_id', 'tags', 'is_published',
        ]);

        $data['is_published'] = $request->boolean('is_published');

        if ($request->input('title') !== $post->title) {
            $data['slug'] = Str::slug($data['title']).random_int(100, 999);
        }

        if ($data['is_published'] && ! $post->is_published) {
            $data['published_at'] = now();
        } elseif (! $data['is_published']) {
            $data['published_at'] = null;
        }

        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }
            $data['thumbnail'] = $this->uploadThumbnail($request->file('thumbnail'));
        }

        $post->update($data);
        $post->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Postingan berhasil diperbarui.',
            'data' => $post,
        ]);
    }

    /**
     * Delete a blog post (admin).
     */
    public function destroy(BlogPost $post): JsonResponse
    {
        if ($post->thumbnail) {
            Storage::disk('public')->delete($post->thumbnail);
        }
        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Postingan berhasil dihapus.',
        ]);
    }

    /**
     * List all categories (public).
     */
    public function categories(): JsonResponse
    {
        $categories = BlogCategory::withCount(['posts' => function ($q) {
            $q->where('is_published', true);
        }])->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * List all categories including post count (admin).
     */
    public function adminCategories(): JsonResponse
    {
        $categories = BlogCategory::withCount('posts')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Store a new category (admin).
     */
    public function storeCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $category = BlogCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori blog berhasil ditambahkan.',
            'data' => $category,
        ], 201);
    }

    /**
     * Update a category (admin).
     */
    public function updateCategory(Request $request, BlogCategory $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name,'.$category->id,
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori blog berhasil diperbarui.',
            'data' => $category,
        ]);
    }

    /**
     * Delete a category (admin).
     */
    public function destroyCategory(BlogCategory $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori blog berhasil dihapus.',
        ]);
    }

    /**
     * Upload image for CKEditor inline content (admin).
     */
    public function uploadImage(Request $request): JsonResponse
    {
        if (! $request->hasFile('upload')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('upload');
        $filename = time().'_blog.webp';
        $path = 'uploads/blog/'.$filename;

        try {
            $manager = new ImageManager(new Driver);
            $img = $manager->read($file);
            $img->resizeDown(1200);
            $encoded = $img->toWebp(85)->toString();
        } catch (\Throwable $e) {
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = time().'_blog.'.$ext;
            $path = 'uploads/blog/'.$filename;
            $encoded = file_get_contents($file->getRealPath());
        }

        Storage::disk('public')->put($path, $encoded);

        try {
            $publicDir = public_path('storage/uploads/blog');
            if (! file_exists($publicDir)) {
                @mkdir($publicDir, 0755, true);
            }
            @file_put_contents($publicDir.'/'.$filename, $encoded);
        } catch (\Throwable $e) {
        }

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    private function uploadThumbnail($file): string
    {
        $filename = time().'_thumb.webp';
        $path = 'uploads/blog/thumbnails/'.$filename;

        try {
            $manager = new ImageManager(new Driver);
            $img = $manager->read($file);
            $img->resizeDown(800);
            $encoded = $img->toWebp(85)->toString();
        } catch (\Throwable $e) {
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = time().'_thumb.'.$ext;
            $path = 'uploads/blog/thumbnails/'.$filename;
            $encoded = file_get_contents($file->getRealPath());
        }

        Storage::disk('public')->put($path, $encoded);

        try {
            $dir1 = public_path('storage/uploads/blog/thumbnails');
            if (! file_exists($dir1)) {
                @mkdir($dir1, 0777, true);
            }
            @file_put_contents($dir1.'/'.$filename, $encoded);

            $dir2 = public_path('uploads/blog/thumbnails');
            if (! file_exists($dir2)) {
                @mkdir($dir2, 0777, true);
            }
            @file_put_contents($dir2.'/'.$filename, $encoded);
        } catch (\Throwable $e) {
        }

        return $path;
    }
}
