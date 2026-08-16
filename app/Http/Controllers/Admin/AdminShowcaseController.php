<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminProduct;
use App\Models\HomeShowcase;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Spatie\ResponseCache\ResponseCache;
use Spatie\ResponseCache\ResponseCache;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

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
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'title' => 'nullable|string|max:255',
            'param_name' => 'nullable|string|max:255',
            'param_value' => 'nullable|array',
            'param_value.*' => 'nullable|string|max:255',
        ]);

        $showcase = HomeShowcase::firstOrCreate([], []);

        if ($request->hasFile('image')) {
            if ($showcase->image) {
                Storage::disk('public')->delete($showcase->image);
            }
            $filename = time().'_showcase.jpg';
            $path = 'uploads/showcase/'.$filename;

            $manager = new ImageManager(new Driver);
            $img = $manager->read($request->file('image'));
            $img->resizeDown(1200);
            $encoded = $img->toJpeg(90)->toString();

            Storage::disk('public')->put($path, $encoded);
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
}
