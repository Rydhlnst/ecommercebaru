<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * JSON cart endpoints for the custom AdminProduct catalogue.
 *
 * Replaces Bagisto's `shop.api.checkout.cart.*` for these products —
 * no `products` table validation, session-backed, returns a fresh cart
 * summary on every mutation so the drawer + badge stay in sync.
 */
class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function show(): JsonResponse
    {
        return response()->json(['cart' => $this->cart->summary()]);
    }

    public function count(): JsonResponse
    {
        return response()->json([
            'count' => $this->cart->count(),
            'items_qty' => $this->cart->itemsQty(),
        ]);
    }

    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:admin_products,id',
            'quantity' => 'nullable|integer|min:1',
            'variation_id' => 'nullable|integer|exists:admin_product_variations,id',
            // Back-compat with the existing card markup, which posts the
            // selected variant under Bagisto's conventional field name.
            'selected_configurable_option' => 'nullable|integer',
        ]);

        $variationId = $validated['variation_id']
            ?? $validated['selected_configurable_option']
            ?? null;

        try {
            return response()->json([
                'success' => true,
                'cart' => $this->cart->add(
                    (int) $validated['product_id'],
                    $variationId ? (int) $variationId : null,
                    (int) ($validated['quantity'] ?? 1),
                ),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        return response()->json([
            'success' => true,
            'cart' => $this->cart->update($validated['key'], (int) $validated['quantity']),
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => 'required|string',
        ]);

        return response()->json([
            'success' => true,
            'cart' => $this->cart->remove($validated['key']),
        ]);
    }
}
