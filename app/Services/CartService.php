<?php

namespace App\Services;

use App\Models\AdminProduct;
use App\Models\AdminProductVariation;
use Illuminate\Support\Facades\Session;

/**
 * Session-backed shopping cart for the custom AdminProduct catalogue.
 *
 * Independent of Bagisto's Webkul cart. Each line is keyed by
 * "{product_id}:{variation_id|0}" so the same product in two weight
 * variants coexist as separate lines.
 */
class CartService
{
    public const SESSION_KEY = 'beres_cart';

    public function all(): array
    {
        return array_values(Session::get(self::SESSION_KEY, []));
    }

    public function add(int $productId, ?int $variationId, int $qty): array
    {
        $product = AdminProduct::with('images')->find($productId);

        if (! $product) {
            throw new \RuntimeException('Produk tidak ditemukan.');
        }

        $variation = null;

        if ($variationId) {
            $variation = AdminProductVariation::where('product_id', $product->id)
                ->where('id', $variationId)
                ->first();

            if (! $variation) {
                throw new \RuntimeException('Variasi tidak valid untuk produk ini.');
            }
        } elseif ($product->has_variations) {
            // No variation chosen on a variant product → auto-pick the first
            // (cheapest) variation, matching the storefront's default selection.
            $variation = $product->variations()->orderBy('weight')->first();
        }

        $unitPrice = $variation?->price ?? $product->price;
        $stock = $variation?->stock ?? $product->stock;
        $key = $product->id.':'.($variation->id ?? 0);
        $qty = max(1, (int) $qty);

        $cart = Session::get(self::SESSION_KEY, []);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = min($cart[$key]['quantity'] + $qty, max(1, $stock), 99);
        } else {
            $cart[$key] = [
                'key' => $key,
                'product_id' => $product->id,
                'variation_id' => $variation?->id,
                'slug' => $product->slug,
                'name' => $product->name.($variation ? ' — '.$variation->weight_label : ''),
                'weight_label' => $variation?->weight_label,
                'price' => (float) $unitPrice,
                'stock' => (int) $stock,
                'image' => $product->images->first()?->image_path,
                'quantity' => min($qty, max(1, $stock), 99),
            ];
        }

        Session::put(self::SESSION_KEY, $cart);

        return $this->summary();
    }

    public function update(string $key, int $qty): array
    {
        $cart = Session::get(self::SESSION_KEY, []);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = min(max(1, (int) $qty), max(1, $cart[$key]['stock'] ?? 99), 99);
            Session::put(self::SESSION_KEY, $cart);
        }

        return $this->summary();
    }

    public function remove(string $key): array
    {
        $cart = Session::get(self::SESSION_KEY, []);
        unset($cart[$key]);
        Session::put(self::SESSION_KEY, $cart);

        return $this->summary();
    }

    public function clear(): void
    {
        Session::put(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return count(Session::get(self::SESSION_KEY, []));
    }

    public function itemsQty(): int
    {
        return (int) array_sum(array_column($this->all(), 'quantity'));
    }

    public function subtotal(): float
    {
        return (float) array_sum(array_map(
            fn ($line) => $line['price'] * $line['quantity'],
            $this->all()
        ));
    }

    /**
     * Serializable cart snapshot consumed by the drawer, checkout page,
     * and the JSON endpoints.
     */
    public function summary(): array
    {
        $lines = $this->all();

        return [
            'count' => count($lines),
            'items_qty' => (int) array_sum(array_column($lines, 'quantity')),
            'subtotal' => $this->subtotal(),
            'items' => array_map(fn ($line) => array_merge($line, [
                'formatted_price' => 'Rp '.number_format($line['price'], 0, ',', '.'),
                'formatted_total' => 'Rp '.number_format($line['price'] * $line['quantity'], 0, ',', '.'),
                'image_url' => $line['image'] ? asset('storage/'.$line['image']) : null,
                'product_url' => route('shop.admin_product.show', $line['slug']),
            ]), $lines),
        ];
    }
}
