<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminCategory;
use App\Models\AdminProduct;
use App\Models\AdminProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = AdminProduct::with('category', 'images')
            ->withCount('images')
            ->latest()
            ->paginate(15);

        return view('admin.product.index', compact('products'));
    }

    public function create()
    {
        $categories = AdminCategory::all();

        return view('admin.product.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:admin_categories,id',
            'badge' => 'nullable|in:new,sale,habis_terjual',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'has_variations' => 'nullable|boolean',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'nullable|in:active,inactive',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'variation_weight' => 'nullable|array',
            'variation_price' => 'nullable|array',
            'variation_stock' => 'nullable|array',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['has_variations'] = $request->boolean('has_variations');
        $validated['status'] = $validated['status'] ?? 'active';

        $product = AdminProduct::create($validated);

        if ($validated['has_variations'] && $request->has('variation_price')) {
            $this->saveVariations($product, $request);
        }

        if ($request->hasFile('images')) {
            $this->saveImages($product, $request->file('images'));
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(AdminProduct $product)
    {
        $categories = AdminCategory::all();
        $product->load('variations', 'images');

        return view('admin.product.edit', compact('product', 'categories'));
    }

    public function update(Request $request, AdminProduct $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:admin_categories,id',
            'badge' => 'nullable|in:new,sale,habis_terjual',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'has_variations' => 'nullable|boolean',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'nullable|in:active,inactive',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'variation_weight' => 'nullable|array',
            'variation_price' => 'nullable|array',
            'variation_stock' => 'nullable|array',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['has_variations'] = $request->boolean('has_variations');
        $validated['status'] = $validated['status'] ?? 'active';

        $product->update($validated);

        if ($validated['has_variations'] && $request->has('variation_price')) {
            $product->variations()->delete();
            $this->saveVariations($product, $request);
        } else {
            $product->variations()->delete();
        }

        if ($request->hasFile('images')) {
            foreach ($product->images as $img) {
                Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }
            $this->saveImages($product, $request->file('images'));
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(AdminProduct $product)
    {
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    private function saveVariations(AdminProduct $product, Request $request): void
    {
        $weights = $request->input('variation_weight', []);
        $prices = $request->input('variation_price', []);
        $stocks = $request->input('variation_stock', []);

        foreach ($prices as $index => $price) {
            if (empty($price)) {
                continue;
            }
            $product->variations()->create([
                'weight' => $weights[$index] ?? 0,
                'price' => $price,
                'stock' => $stocks[$index] ?? 0,
            ]);
        }

        $firstVariation = $product->variations()->first();
        if ($firstVariation) {
            $product->update([
                'price' => $firstVariation->price,
                'stock' => $product->variations()->sum('stock'),
            ]);
        }
    }

    private function saveImages(AdminProduct $product, array $files): void
    {
        $manager = new ImageManager(new Driver);

        foreach ($files as $index => $file) {
            $filename = time().'_'.uniqid().'_'.$index.'.webp';
            $path = 'uploads/products/'.$filename;

            $img = $manager->read($file);
            $img->resizeDown(800);
            $encoded = $img->toWebp(80)->toString();

            Storage::disk('public')->put($path, $encoded);

            AdminProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'sort_order' => $index,
            ]);
        }
    }
}
