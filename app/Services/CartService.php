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
        return array_values($this->sessionCart());
    }

    public function add(int $productId, ?int $variationId, int $qty): array
    {
        $product = AdminProduct::with('images')->find($productId);

        if (! $product) {
            throw new \RuntimeException('Produk tidak ditemukan.');
        }

        if ($product->status !== 'active') {
            throw new \RuntimeException('Produk tidak tersedia.');
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

        if ((int) $stock < 1) {
            throw new \RuntimeException('Stok produk habis.');
        }

        $key = $product->id.':'.($variation->id ?? 0);
        $qty = max(1, (int) $qty);

        $cart = $this->sessionCart();

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = min($cart[$key]['quantity'] + $qty, (int) $stock, 99);
        } else {
            $cart[$key] = [
                'key' => $key,
                'product_id' => $product->id,
                'variation_id' => $variation?->id,
                'slug' => $product->slug,
                'name' => $product->name.($variation ? ' — '.$variation->weight_label : ''),
                'weight_label' => $variation?->weight_label,
                'weight' => $variation?->weight,
                'price' => (float) $unitPrice,
                'stock' => (int) $stock,
                'image' => $product->images->first()?->image_path,
                'quantity' => min($qty, (int) $stock, 99),
            ];
        }

        Session::put(self::SESSION_KEY, $cart);

        return $this->summary();
    }

    public function update(string $key, int $qty): array
    {
        $cart = $this->sessionCart();

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = min(max(1, (int) $qty), max(1, $cart[$key]['stock'] ?? 99), 99);
            Session::put(self::SESSION_KEY, $cart);
        }

        return $this->summary();
    }

    public function remove(string $key): array
    {
        $cart = $this->sessionCart();
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
        return (int) array_sum(array_map(
            fn (array $line) => (int) ($line['quantity'] ?? 0),
            $this->all()
        ));
    }

    public function subtotal(): float
    {
        return (float) array_sum(array_map(
            fn (array $line) => (float) ($line['price'] ?? 0) * (int) ($line['quantity'] ?? 0),
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
            'items_qty' => $this->itemsQty(),
            'subtotal' => $this->subtotal(),
            'items' => array_map(fn (array $line) => array_merge($line, [
                'formatted_price' => 'Rp '.number_format((float) ($line['price'] ?? 0), 0, ',', '.'),
                'formatted_total' => 'Rp '.number_format((float) ($line['price'] ?? 0) * (int) ($line['quantity'] ?? 0), 0, ',', '.'),
                'image_url' => ! empty($line['image']) ? asset('storage/'.$line['image']) : null,
                'product_url' => ! empty($line['slug']) ? route('shop.admin_product.show', $line['slug']) : null,
            ]), $lines),
        ];
    }

    /**
     * Discard malformed lines left by an older cart schema instead of letting
     * a stale browser session crash the cart response.
     */
    private function sessionCart(): array
    {
        $cart = Session::get(self::SESSION_KEY, []);

        if (! is_array($cart)) {
            return [];
        }

        return array_filter($cart, fn ($line) => is_array($line));
    }
}
