<?php

namespace Beres\Product\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Product\Services\ProductService;
use Beres\Product\Services\ProductImportService;
use Beres\Product\DTOs\ProductBulkActionDTO;
use Webkul\Product\Models\Product;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected ProductImportService $importService
    ) {}

    /**
     * Display product listing.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'name', 'sku', 'status', 'min_price', 'max_price',
            'category_id', 'sort_by', 'sort_order', 'per_page',
        ]);

        $products = $this->productService->search($filters);

        return view('beres-product::products.index', [
            'products' => $products,
            'filters'  => $filters,
        ]);
    }

    /**
     * Display product detail.
     */
    public function show($id)
    {
        $product = Product::with(['categories', 'images', 'attribute_values'])->findOrFail($id);
        $productDto = $this->productService->getProductDto($product);
        $activityLog = $this->productService->getActivityLog($id);

        return view('beres-product::products.show', [
            'product'     => $productDto,
            'activityLog' => $activityLog,
        ]);
    }

    /**
     * Bulk action on products.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'action'      => 'required|string|in:activate,deactivate,delete,update_price,update_quantity',
            'data'        => 'nullable|array',
        ]);

        $dto = ProductBulkActionDTO::fromArray($request->only([
            'product_ids', 'action', 'data',
        ]));

        $results = $this->productService->bulkAction($dto);

        return response()->json([
            'success' => true,
            'data'    => $results,
        ]);
    }

    /**
     * Import products from CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv|max:10240',
        ]);

        $file = $request->file('file');
        $filePath = $file->storeAs('imports', 'products_' . time() . '.csv');

        $userId = auth()->guard('admin')->id();
        $results = $this->importService->importFromCsv(storage_path('app/' . $filePath), $userId);

        return response()->json([
            'success' => true,
            'data'    => $results,
        ]);
    }

    /**
     * Export products to CSV.
     */
    public function export(Request $request)
    {
        $filters = $request->only(['ids']);
        $filePath = $this->importService->exportToCsv($filters);

        return response()->download($filePath, 'products_export_' . date('Y-m-d_His') . '.csv', [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    }
}
