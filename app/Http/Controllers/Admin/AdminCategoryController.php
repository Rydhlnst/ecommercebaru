<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Spatie\ResponseCache\Facades\ResponseCache;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = new LengthAwarePaginator(collect(), 0, 15, 1);

        try {
            if (Schema::hasTable('admin_categories')) {
                $categories = AdminCategory::with('parent', 'products')
                    ->withCount('products')
                    ->withCount('children')
                    ->latest()
                    ->paginate(15);
            }
        } catch (QueryException $e) {
            // Table might not exist yet
        }

        return view('admin.category.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = collect();

        try {
            if (Schema::hasTable('admin_categories')) {
                $parentCategories = AdminCategory::whereNull('parent_id')->get();
            }
        } catch (QueryException $e) {
            // Table might not exist yet
        }

        return view('admin.category.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:admin_categories,name',
            'parent_id' => 'nullable|exists:admin_categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['parent_id'] = ! empty($validated['parent_id']) ? (int) $validated['parent_id'] : null;

        if ($request->hasFile('image')) {
            try {
                $validated['image'] = $this->compressImage($request->file('image'));
            } catch (\Throwable $e) {
                return back()->withInput()->with('error', 'Category image could not be uploaded. Please check storage permissions.');
            }
        }

        AdminCategory::create($validated);

        ResponseCache::clear();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $category = AdminCategory::findOrFail($id);
        $parentCategories = AdminCategory::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();

        return view('admin.category.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, $id)
    {
        $category = AdminCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:admin_categories,name,'.$category->id,
            'parent_id' => 'nullable|exists:admin_categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['parent_id'] = ! empty($validated['parent_id']) ? (int) $validated['parent_id'] : null;

        if ($validated['parent_id'] && $validated['parent_id'] == $category->id) {
            return back()->withInput()->with('error', 'Kategori tidak bisa menjadi induk dari dirinya sendiri.');
        }

        if ($request->hasFile('image')) {
            if ($category->image) {
                $this->deleteStoredFile($category->image);
            }
            try {
                $validated['image'] = $this->compressImage($request->file('image'));
            } catch (\Throwable $e) {
                return back()->withInput()->with('error', 'Category image could not be replaced. Please check storage permissions.');
            }
        }

        $category->update($validated);

        ResponseCache::clear();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $category = AdminCategory::findOrFail($id);

        if ($category->products()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus kategori yang masih memiliki produk.');
        }

        if ($category->children()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus kategori yang masih memiliki sub-kategori.');
        }

        if ($category->image) {
            $this->deleteStoredFile($category->image);
        }

        $category->delete();
        ResponseCache::clear();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }

    public function clearAll()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            AdminCategory::query()->each(function (AdminCategory $category) {
                $this->deleteStoredFile($category->image);
            });
            AdminCategory::truncate();
            if (Schema::hasTable('homepage_highlights')) {
                DB::table('homepage_highlights')->where('section', 'categories')->delete();
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->route('admin.categories.index')->with('success', 'Seluruh data kategori berhasil dikosongkan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengosongkan kategori: '.$e->getMessage());
        }
    }

    private function compressImage($file)
    {
        try {
            $manager = new ImageManager(new Driver);
        } catch (\Throwable $e) {
            $manager = null;
        }

        $filename = time().'_'.uniqid().'.jpg';
        $path = 'uploads/categories/'.$filename;

        $encoded = null;
        if ($manager) {
            try {
                $img = $manager->read($file);
                $img->resizeDown(800);
                $encoded = $img->toJpeg(90)->toString();
            } catch (\Throwable $e) {
                $encoded = null;
            }
        }

        if ($encoded === null) {
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = time().'_'.uniqid().'.'.$ext;
            $path = 'uploads/categories/'.$filename;
            $encoded = file_get_contents($file->getRealPath());
        }

        if (! Storage::disk('public')->put($path, $encoded)) {
            throw new \RuntimeException('Unable to write category image to the public storage disk.');
        }

        // Also write directly to public/storage AND public/uploads for cPanel environments
        try {
            $dir1 = public_path('storage/uploads/categories');
            if (! is_dir($dir1) && ! @mkdir($dir1, 0777, true) && ! is_dir($dir1)) {
                throw new \RuntimeException('Unable to create the category upload directory.');
            }
            if (@file_put_contents($dir1.'/'.$filename, $encoded) === false) {
                throw new \RuntimeException('Unable to publish the category image.');
            }

            $dir2 = public_path('uploads/categories');
            if (! is_dir($dir2) && ! @mkdir($dir2, 0777, true) && ! is_dir($dir2)) {
                throw new \RuntimeException('Unable to create the category public upload directory.');
            }
            if (@file_put_contents($dir2.'/'.$filename, $encoded) === false) {
                throw new \RuntimeException('Unable to publish the category public image.');
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
