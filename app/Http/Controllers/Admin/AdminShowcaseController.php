<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminProduct;
use App\Models\HomeShowcase;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Spatie\ResponseCache\Facades\ResponseCache;

class AdminShowcaseController extends Controller
{
    public function index()
    {
        $showcase = null;
        $products = collect();

        try {
            if (Schema::hasTable('home_showcases')) {
                $showcase = HomeShowcase::first();
            }
        } catch (QueryException $e) {
            // Table might not exist yet
        }

        try {
            if (Schema::hasTable('admin_products')) {
                $products = AdminProduct::all();
            }
        } catch (QueryException $e) {
            // Table might not exist yet
        }

        return view('admin.showcase.index', compact('showcase', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'nullable|exists:admin_products,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'title' => 'nullable|string|max:255',
            'param_name' => 'nullable|string|max:255',
            'param_value' => 'nullable|array',
            'param_value.*' => 'nullable|string|max:255',
        ]);

        $showcase = HomeShowcase::firstOrCreate([], []);

        if ($request->hasFile('image')) {
            if ($showcase->image) {
                $this->deleteStoredFile($showcase->image);
            }
            $filename = time().'_'.uniqid().'_showcase.jpg';
            $path = 'uploads/showcase/'.$filename;

            try {
                $manager = new ImageManager(new Driver);
                $img = $manager->read($request->file('image'));
                $img->resizeDown(1200);
                $encoded = $img->toJpeg(90)->toString();

                if (! Storage::disk('public')->put($path, $encoded)) {
                    throw new \RuntimeException('Unable to write showcase image to the public storage disk.');
                }

                foreach ([public_path('storage/uploads/showcase'), public_path('uploads/showcase')] as $directory) {
                    if (! is_dir($directory) && ! @mkdir($directory, 0777, true) && ! is_dir($directory)) {
                        throw new \RuntimeException('Unable to create the showcase upload directory.');
                    }

                    if (@file_put_contents($directory.'/'.$filename, $encoded) === false) {
                        throw new \RuntimeException('Unable to publish the showcase image.');
                    }
                }
            } catch (\Throwable $e) {
                report($e);

                return back()->withInput()->with('error', 'Showcase image could not be uploaded. Please check storage permissions.');
            }

            $showcase->image = $path;
        }

        $showcase->product_id = $validated['product_id'];
        $showcase->title = $validated['param_name'] ?? $showcase->title;

        $items = collect($validated['param_value'] ?? [])
            ->filter(fn ($item) => ! empty($item))
            ->take(8)
            ->values()
            ->toArray();

        $showcase->items = $items;
        $showcase->save();

        ResponseCache::clear();

        return redirect()->route('admin.showcase.index')->with('success', 'Showcase berhasil diperbarui.');
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
