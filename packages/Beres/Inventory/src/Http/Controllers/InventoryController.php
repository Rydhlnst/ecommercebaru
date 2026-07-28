<?php

namespace Beres\Inventory\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Inventory\Services\InventoryService;
use Webkul\Product\Models\Product;
use Illuminate\Support\Facades\Response;

class InventoryController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Display inventory listing.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'name', 'sku', 'low_stock', 'out_of_stock',
            'sort_by', 'sort_order', 'per_page',
        ]);

        $products = $this->inventoryService->search($filters);
        $stats = $this->inventoryService->getStats();

        return view('beres-inventory::inventory.index', [
            'products' => $products,
            'stats'    => $stats,
            'filters'  => $filters,
        ]);
    }

    /**
     * Adjust stock for a product.
     */
    public function adjustStock(Request $request)
    {
        $request->validate([
            'product_id'          => 'required|exists:products,id',
            'quantity'            => 'required|integer|min:0',
            'action'              => 'required|string|in:add,subtract,set',
            'inventory_source_id' => 'nullable|exists:inventory_sources,id',
            'note'                => 'nullable|string|max:500',
        ]);

        $result = $this->inventoryService->adjustStock(
            $request->input('product_id'),
            $request->input('quantity'),
            $request->input('action'),
            $request->input('inventory_source_id'),
            $request->input('note')
        );

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to adjust stock.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Stock adjusted successfully.',
        ]);
    }

    /**
     * Get stock history for a product.
     */
    public function stockHistory($productId)
    {
        $history = $this->inventoryService->getStockHistory($productId);

        return response()->json([
            'success' => true,
            'data'    => $history,
        ]);
    }

    /**
     * Get low stock products.
     */
    public function lowStock()
    {
        $products = $this->inventoryService->getLowStockProducts();

        return response()->json([
            'success' => true,
            'data'    => $products,
        ]);
    }

    /**
     * Get inventory statistics.
     */
    public function stats()
    {
        $stats = $this->inventoryService->getStats();

        return response()->json([
            'success' => true,
            'data'    => $stats->toArray(),
        ]);
    }
}
