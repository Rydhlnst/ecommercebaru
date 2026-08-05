<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = AdminCategory::with('parent', 'products')
            ->withCount('products')
            ->withCount('children')
            ->latest()
            ->paginate(15);

        return view('admin.category.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = AdminCategory::whereNull('parent_id')->get();

        return view('admin.category.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:admin_categories,name',
            'parent_id' => 'nullable|exists:admin_categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

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
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($validated['parent_id'] == $category->id) {
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
