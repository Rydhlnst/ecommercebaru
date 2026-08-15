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
            $validated['image'] = $this->compressImage($request->file('image'));
        }

        AdminCategory::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(AdminCategory $category)
    {
        $parentCategories = AdminCategory::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();

        return view('admin.category.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, AdminCategory $category)
    {
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
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $this->compressImage($request->file('image'));
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(AdminCategory $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus kategori yang masih memiliki produk.');
        }

        if ($category->children()->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus kategori yang masih memiliki sub-kategori.');
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }

    public function clearAll()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
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
        $filename = time().'_'.uniqid().'.webp';
        $path = 'uploads/categories/'.$filename;

        $manager = new ImageManager(new Driver);
        $img = $manager->read($file);
        $img->resizeDown(500);
        $encoded = $img->toWebp(80)->toString();

        Storage::disk('public')->put($path, $encoded);

        return $path;
    }
}
