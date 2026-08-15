<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminCategory;
use App\Models\AdminProduct;
use App\Models\AdminProductImage;
use App\Models\AdminProductVariation;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = new LengthAwarePaginator(collect(), 0, 15, 1);

        try {
            if (Schema::hasTable('admin_products')) {
                $products = AdminProduct::with('category', 'images')
                    ->withCount('images')
                    ->latest()
                    ->paginate(15);
            }
        } catch (QueryException $e) {
            // Table might not exist yet
        }

        return view('admin.product.index', compact('products'));
    }

    public function create()
    {
        $categories = collect();

        try {
            if (Schema::hasTable('admin_categories')) {
                $categories = AdminCategory::all();
            }
        } catch (QueryException $e) {
            // Table might not exist yet
        }

        return view('admin.product.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $hasVariations = $request->boolean('has_variations');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:admin_categories,id',
            'badge' => 'nullable|in:new,sale,habis_terjual',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'has_variations' => 'nullable|boolean',
            'price' => $hasVariations ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'stock' => $hasVariations ? 'nullable|integer|min:0' : 'required|integer|min:0',
            'status' => 'nullable|in:active,inactive',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10240',
            'variation_weight' => 'nullable|array',
            'variation_price' => 'nullable|array',
            'variation_stock' => 'nullable|array',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['has_variations'] = $hasVariations;
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['price'] = $validated['price'] ?? 0;
        $validated['stock'] = $validated['stock'] ?? 0;

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
        $hasVariations = $request->boolean('has_variations');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:admin_categories,id',
            'badge' => 'nullable|in:new,sale,habis_terjual',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'has_variations' => 'nullable|boolean',
            'price' => $hasVariations ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'stock' => $hasVariations ? 'nullable|integer|min:0' : 'required|integer|min:0',
            'status' => 'nullable|in:active,inactive',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10240',
            'variation_weight' => 'nullable|array',
            'variation_price' => 'nullable|array',
            'variation_stock' => 'nullable|array',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['has_variations'] = $hasVariations;
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['price'] = $validated['price'] ?? 0;
        $validated['stock'] = $validated['stock'] ?? 0;

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

    public function clearAll()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            if (Schema::hasTable('admin_product_images')) {
                AdminProductImage::truncate();
            }
            if (Schema::hasTable('admin_product_variations')) {
                AdminProductVariation::truncate();
            }
            AdminProduct::truncate();
            if (Schema::hasTable('homepage_highlights')) {
                DB::table('homepage_highlights')->where('section', '!=', 'categories')->delete();
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->route('admin.products.index')->with('success', 'Seluruh data produk dan variasi berhasil dikosongkan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengosongkan produk: '.$e->getMessage());
        }
    }
}
