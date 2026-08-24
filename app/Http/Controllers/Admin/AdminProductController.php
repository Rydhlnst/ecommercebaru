<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveAdminProductRequest;
use App\Models\AdminCategory;
use App\Models\AdminProduct;
use App\Models\AdminProductImage;
use App\Models\AdminProductVariation;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    public function store(SaveAdminProductRequest $request)
    {
        $hasVariations = $request->boolean('has_variations');

        $productData = Arr::only($request->validated(), [
            'name',
            'category_id',
            'badge',
            'description',
            'price',
            'compare_at_price',
            'stock',
            'status',
        ]);

        $productData['is_featured'] = $request->boolean('is_featured');
        $productData['has_variations'] = $hasVariations;
        $productData['status'] = $productData['status'] ?? 'active';
        $productData['price'] = $productData['price'] ?? 0;
        $productData['stock'] = $productData['stock'] ?? 0;
        $productData['compare_at_price'] = $productData['compare_at_price'] ?? null;

        try {
            [$product, $filesToDelete] = DB::transaction(function () use ($productData, $request) {
                $product = AdminProduct::create($productData);

                if ($productData['has_variations']) {
                    $this->saveVariations($product, $request);
                }

                $filesToDelete = $this->syncProductImages($product, $request);

                return [$product, $filesToDelete];
            });

            foreach ($filesToDelete as $path) {
                $this->deleteStoredFile($path);
            }
        } catch (\Throwable $e) {
            Log::error('Product creation failed.', [
                'exception' => $e,
            ]);

            return back()->withInput()->with('error', 'Product could not be created. Please review the data and try again.');
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

    public function update(SaveAdminProductRequest $request, $product)
    {
        if (! ($product instanceof AdminProduct)) {
            $product = AdminProduct::where('slug', $product)
                ->orWhere('id', $product)
                ->firstOrFail();
        }

        $hasVariations = $request->boolean('has_variations');

        $productData = Arr::only($request->validated(), [
            'name',
            'category_id',
            'badge',
            'description',
            'price',
            'compare_at_price',
            'stock',
            'status',
        ]);

        $productData['is_featured'] = $request->boolean('is_featured');
        $productData['has_variations'] = $hasVariations;
        $productData['status'] = $productData['status'] ?? 'active';
        $productData['price'] = $productData['price'] ?? 0;
        $productData['stock'] = $productData['stock'] ?? 0;
        $productData['compare_at_price'] = $productData['compare_at_price'] ?? null;

        try {
            [$filesToDelete] = DB::transaction(function () use ($product, $productData, $request) {
                $product->update($productData);

                $product->variations()->delete();
                if ($productData['has_variations']) {
                    $this->saveVariations($product, $request);
                }

                return [$this->syncProductImages($product, $request)];
            });

            foreach ($filesToDelete as $path) {
                $this->deleteStoredFile($path);
            }
        } catch (\Throwable $e) {
            Log::error('Product update failed.', [
                'product_id' => $product->id,
                'exception' => $e,
            ]);

            return back()->withInput()->with('error', 'Product could not be updated. Please review the data and try again.');
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
            foreach ($this->imagePaths($img) as $path) {
                $this->deleteStoredFile($path);
            }
        }

        if (Schema::hasTable('homepage_highlights')) {
            DB::table('homepage_highlights')
                ->where('entity_type', 'product')
                ->where('entity_id', $product->id)
                ->delete();
        }

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
            $comparePrice = trim((string) ($comparePrices[$index] ?? ''));

            $product->variations()->create([
                'weight' => $weights[$index] ?? 0,
                'price' => $price,
                'compare_at_price' => $comparePrice === '' ? null : $comparePrice,
                'stock' => $stocks[$index] ?? 0,
            ]);
        }

        $firstVariation = $product->variations()
            ->orderBy('price')
            ->orderBy('weight')
            ->first();
        if ($firstVariation) {
            $product->update([
                'price' => $firstVariation->price,
                'compare_at_price' => $firstVariation->compare_at_price,
                'stock' => $product->variations()->sum('stock'),
            ]);
        }
    }

    private function syncProductImages(AdminProduct $product, Request $request): array
    {
        $meta = json_decode((string) $request->input('image_meta', ''), true);
        $files = array_values($request->file('images', []));
        $existing = $product->images()->get()->keyBy('id');
        $filesToDelete = [];

        if (! $request->has('image_meta') && ! $request->hasFile('images') && ! $request->has('remove_image_ids')) {
            return [];
        }

        if (! is_array($meta)) {
            $meta = [];
            foreach ($files as $index => $file) {
                $meta[] = ['file_index' => $index];
            }
        }

        if ($existing->isNotEmpty() && $meta === [] && $files === [] && ! $request->has('remove_image_ids')) {
            return [];
        }

        $keptIds = collect($meta)
            ->pluck('id')
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->all();
        $removeIds = collect($request->input('remove_image_ids', []))
            ->map(fn ($id) => (int) $id)
            ->merge($existing->keys()->diff($keptIds))
            ->unique()
            ->all();

        foreach ($removeIds as $id) {
            $image = $existing->get($id);
            if ($image) {
                $filesToDelete = array_merge($filesToDelete, $this->imagePaths($image));
                $image->delete();
            }
        }

        foreach (array_values($meta) as $sortOrder => $item) {
            $image = isset($item['id']) ? $existing->get((int) $item['id']) : null;
            $fileIndex = isset($item['file_index']) ? (int) $item['file_index'] : null;

            if (! $image && $fileIndex !== null && isset($files[$fileIndex])) {
                $image = $this->storeProductImage($product, $files[$fileIndex], $item, $sortOrder);
            }

            if (! $image) {
                continue;
            }

            $image->update(array_merge(
                $this->imagePresentationData($item),
                ['sort_order' => $sortOrder]
            ));
        }

        return $filesToDelete;
    }

    private function storeProductImage(AdminProduct $product, $file, array $meta, int $sortOrder): AdminProductImage
    {
        $manager = new ImageManager(new Driver);
        $image = $manager->read($file->getRealPath())->orient();
        $directory = 'uploads/products/'.$product->id.'/'.Str::uuid();
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $originalPath = $directory.'/original.'.$extension;
        $originalContents = file_get_contents($file->getRealPath());

        if ($originalContents === false) {
            throw new \RuntimeException('Unable to read uploaded product image.');
        }

        $this->publishProductFile($originalPath, $originalContents);

        $derivatives = [];
        foreach ([480, 800, 1600] as $size) {
            $variant = clone $image;
            $contents = $variant->resizeDown($size)->toWebp(85)->toString();
            $path = $directory.'/'.$size.'.webp';
            $this->publishProductFile($path, $contents);
            $derivatives['image_'.$size.'_path'] = $path;
        }

        $presentation = $this->imagePresentationData($meta);
        $presentation['alt_text'] = $presentation['alt_text'] ?: $product->name;

        return $product->images()->create(array_merge([
            'image_path' => $originalPath,
            'width' => $image->width(),
            'height' => $image->height(),
            'sort_order' => $sortOrder,
        ], $derivatives, $presentation));
    }

    private function imagePresentationData(array $meta): array
    {
        return [
            'fit_mode' => ($meta['fit_mode'] ?? 'cover') === 'contain' ? 'contain' : 'cover',
            'focal_x' => max(0, min(100, (int) ($meta['focal_x'] ?? 50))),
            'focal_y' => max(0, min(100, (int) ($meta['focal_y'] ?? 50))),
            'alt_text' => isset($meta['alt_text']) ? trim((string) $meta['alt_text']) : null,
        ];
    }

    private function publishProductFile(string $path, string $contents): void
    {
        if (! Storage::disk('public')->put($path, $contents)) {
            throw new \RuntimeException('Unable to write product image to public storage.');
        }

        foreach ([public_path('storage/'.$path), public_path($path)] as $destination) {
            $directory = dirname($destination);
            if (! is_dir($directory) && ! @mkdir($directory, 0777, true) && ! is_dir($directory)) {
                throw new \RuntimeException('Unable to create the product image directory.');
            }
            if (@file_put_contents($destination, $contents) === false) {
                throw new \RuntimeException('Unable to publish the product image.');
            }
        }
    }

    private function imagePaths(AdminProductImage $image): array
    {
        return array_values(array_filter([
            $image->image_path,
            $image->image_480_path,
            $image->image_800_path,
            $image->image_1600_path,
        ]));
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

    public function clearAll()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            if (Schema::hasTable('admin_product_images')) {
                AdminProductImage::query()->each(function (AdminProductImage $image) {
                    foreach ($this->imagePaths($image) as $path) {
                        $this->deleteStoredFile($path);
                    }
                });
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
