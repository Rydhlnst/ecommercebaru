<?php

namespace Beres\Account\Services;

use Webkul\Customer\Models\Customer;
use Webkul\Sales\Models\Order;
use Webkul\Product\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AccountService
{
    /**
     * Get customer profile.
     */
    public function getProfile(int $customerId): ?Customer
    {
        return Customer::find($customerId);
    }

    /**
     * Update customer profile.
     */
    public function updateProfile(int $customerId, array $data): bool
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            return false;
        }

        return $customer->update($data);
    }

    /**
     * Change customer password.
     */
    public function changePassword(int $customerId, string $currentPassword, string $newPassword): bool
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            return false;
        }

        if (!Hash::check($currentPassword, $customer->password)) {
            return false;
        }

        return $customer->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    /**
     * Get customer addresses.
     */
    public function getAddresses(int $customerId): array
    {
        return Customer::find($customerId)
            ->addresses
            ->toArray();
    }

    /**
     * Add customer address.
     */
    public function addAddress(int $customerId, array $data): ?object
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            return null;
        }

        return $customer->addresses()->create($data);
    }

    /**
     * Update customer address.
     */
    public function updateAddress(int $customerId, int $addressId, array $data): bool
    {
        $address = Customer::find($customerId)
            ->addresses()
            ->find($addressId);

        if (!$address) {
            return false;
        }

        return $address->update($data);
    }

    /**
     * Delete customer address.
     */
    public function deleteAddress(int $customerId, int $addressId): bool
    {
        $address = Customer::find($customerId)
            ->addresses()
            ->find($addressId);

        if (!$address) {
            return false;
        }

        return $address->delete();
    }

    /**
     * Get customer order history.
     */
    public function getOrderHistory(int $customerId, int $limit = 20): array
    {
        return Order::where('customer_id', $customerId)
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get customer order detail.
     */
    public function getOrderDetail(int $customerId, int $orderId): ?object
    {
        return Order::where('customer_id', $customerId)
            ->with(['items', 'invoices', 'shipments'])
            ->find($orderId);
    }

    /**
     * Get customer wishlist.
     */
    public function getWishlist(int $customerId): array
    {
        return Customer::find($customerId)
            ->wishlist_items()
            ->with('product')
            ->get()
            ->toArray();
    }

    /**
     * Add product to wishlist.
     */
    public function addToWishlist(int $customerId, int $productId): bool
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            return false;
        }

        $product = Product::find($productId);

        if (!$product) {
            return false;
        }

        // Check if already in wishlist
        $exists = $customer->wishlist_items()
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            return false;
        }

        $customer->wishlist_items()->create([
            'product_id' => $productId,
        ]);

        return true;
    }

    /**
     * Remove product from wishlist.
     */
    public function removeFromWishlist(int $customerId, int $productId): bool
    {
        return Customer::find($customerId)
            ->wishlist_items()
            ->where('product_id', $productId)
            ->delete() > 0;
    }

    /**
     * Get customer statistics.
     */
    public function getStats(int $customerId): array
    {
        $customer = Customer::find($customerId);

        if (!$customer) {
            return [];
        }

        $orders = $customer->orders();

        return [
            'total_orders'   => $orders->count(),
            'total_spent'    => (float) $orders->sum('grand_total'),
            'wishlist_count' => $customer->wishlist_items()->count(),
            'address_count'  => $customer->addresses()->count(),
        ];
    }
}
