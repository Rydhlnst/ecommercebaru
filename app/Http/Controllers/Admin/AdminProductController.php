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
use Spatie\ResponseCache\Facades\ResponseCache;

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
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock' => $hasVariations ? 'nullable|integer|min:0' : 'required|integer|min:0',
            'status' => 'nullable|in:active,inactive',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10240',
            'variation_weight' => 'nullable|array',
            'variation_price' => 'nullable|array',
            'variation_compare_at_price' => 'nullable|array',
            'variation_stock' => 'nullable|array',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['has_variations'] = $hasVariations;
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['price'] = $validated['price'] ?? 0;
        $validated['stock'] = $validated['stock'] ?? 0;
        $validated['compare_at_price'] = $validated['compare_at_price'] ?? null;

        $product = AdminProduct::create($validated);

        if ($validated['has_variations'] && $request->has('variation_price')) {
            $this->saveVariations($product, $request);
        }

        if ($request->hasFile('images')) {
            $this->saveImages($product, $request->file('images'));
        }

        ResponseCache::clear();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit($product)
    {
        if (! ($product instanceof AdminProduct)) {
            $product = AdminProduct::where('slug', $product)
                ->orWhere('id', $product)
                ->firstOrFail();
        }

        $categories = AdminCategory::all();
        $product->load('variations', 'images');

        return view('admin.product.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $product)
    {
        if (! ($product instanceof AdminProduct)) {
            $product = AdminProduct::where('slug', $product)
                ->orWhere('id', $product)
                ->firstOrFail();
        }

        $hasVariations = $request->boolean('has_variations');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:admin_categories,id',
            'badge' => 'nullable|in:new,sale,habis_terjual',
            'description' => 'nullable|string',
            'is_featured' => 'nullable|boolean',
            'has_variations' => 'nullable|boolean',
            'price' => $hasVariations ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock' => $hasVariations ? 'nullable|integer|min:0' : 'required|integer|min:0',
            'status' => 'nullable|in:active,inactive',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10240',
            'variation_weight' => 'nullable|array',
            'variation_price' => 'nullable|array',
            'variation_compare_at_price' => 'nullable|array',
            'variation_stock' => 'nullable|array',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['has_variations'] = $hasVariations;
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['price'] = $validated['price'] ?? 0;
        $validated['stock'] = $validated['stock'] ?? 0;
        $validated['compare_at_price'] = $validated['compare_at_price'] ?? null;

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

        ResponseCache::clear();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($product)
    {
        if (! ($product instanceof AdminProduct)) {
            $product = AdminProduct::where('slug', $product)
                ->orWhere('id', $product)
                ->firstOrFail();
        }

        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        \DB::table('homepage_highlights')
            ->where('entity_type', 'product')
            ->where('entity_id', $product->id)
            ->delete();

        $product->delete();
        ResponseCache::clear();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    private function saveVariations(AdminProduct $product, Request $request): void
    {
        $weights = $request->input('variation_weight', []);
        $prices = $request->input('variation_price', []);
        $comparePrices = $request->input('variation_compare_at_price', []);
        $stocks = $request->input('variation_stock', []);

        foreach ($prices as $index => $price) {
            if (empty($price)) {
                continue;
            }
            $product->variations()->create([
                'weight' => $weights[$index] ?? 0,
                'price' => $price,
                'compare_at_price' => $comparePrices[$index] ?? null,
                'stock' => $stocks[$index] ?? 0,
            ]);
        }

        $firstVariation = $product->variations()->first();
        if ($firstVariation) {
            $product->update([
                'price' => $firstVariation->price,
                'compare_at_price' => $firstVariation->compare_at_price,
                'stock' => $product->variations()->sum('stock'),
            ]);
        }
    }

    private function saveImages(AdminProduct $product, array $files): void
    {
        try {
            $manager = new ImageManager(new Driver);
        } catch (\Throwable $e) {
            $manager = null;
        }

        foreach ($files as $index => $file) {
            $filename = time().'_'.uniqid().'_'.$index.'.jpg';
            $path = 'uploads/products/'.$filename;

            $encoded = null;
            if ($manager) {
                try {
                    $img = $manager->read($file);
                    $img->resizeDown(1600);
                    $encoded = $img->toJpeg(92)->toString();
                } catch (\Throwable $e) {
                    $encoded = null;
                }
            }

            if ($encoded === null) {
                $ext = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = time().'_'.uniqid().'_'.$index.'.'.$ext;
                $path = 'uploads/products/'.$filename;
                $encoded = file_get_contents($file->getRealPath());
            }

            Storage::disk('public')->put($path, $encoded);

            // Also write directly to public/storage AND public/uploads for cPanel environments
            try {
                $dir1 = public_path('storage/uploads/products');
                if (! file_exists($dir1)) {
                    @mkdir($dir1, 0777, true);
                }
                @file_put_contents($dir1.'/'.$filename, $encoded);

                $dir2 = public_path('uploads/products');
                if (! file_exists($dir2)) {
                    @mkdir($dir2, 0777, true);
                }
                @file_put_contents($dir2.'/'.$filename, $encoded);
            } catch (\Throwable $e) {
                // Ignore fallback error
            }

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
