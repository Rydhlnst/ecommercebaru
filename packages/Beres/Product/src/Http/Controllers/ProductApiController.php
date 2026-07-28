<?php

namespace Beres\Product\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Product\Services\ProductService;
use Webkul\Product\Models\Product;
use Illuminate\Support\Facades\Response;

class ProductApiController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    /**
     * Get products list.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'name', 'sku', 'status', 'min_price', 'max_price',
            'category_id', 'sort_by', 'sort_order', 'per_page',
        ]);

        $products = $this->productService->search($filters);

        return response()->json([
            'success' => true,
            'data'    => $products,
        ]);
    }

    /**
     * Get product detail.
     */
    public function show($id)
    {
        $product = Product::with(['categories', 'images', 'attribute_values'])->findOrFail($id);
        $productDto = $this->productService->getProductDto($product);

        return response()->json([
            'success' => true,
            'data'    => $productDto->toArray(),
        ]);
    }

    /**
     * Get product activity log.
     */
    public function activityLog($id)
    {
        $activityLog = $this->productService->getActivityLog($id);

        return response()->json([
            'success' => true,
            'data'    => $activityLog,
        ]);
    }
}
