<?php

namespace Beres\Account\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Beres\Account\Services\AccountService;
use Webkul\Customer\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function __construct(
        protected AccountService $accountService
    ) {}

    /**
     * Display customer dashboard.
     */
    public function index()
    {
        $customer = auth()->guard('customer')->user();
        $stats = $this->accountService->getStats($customer->id);

        return view('beres-account::account.index', [
            'customer' => $customer,
            'stats'    => $stats,
        ]);
    }

    /**
     * Display edit profile form.
     */
    public function profile()
    {
        $customer = auth()->guard('customer')->user();

        return view('beres-account::account.profile', [
            'customer' => $customer,
        ]);
    }

    /**
     * Update customer profile.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'phone'      => 'nullable|string|max:20',
        ]);

        $customer = auth()->guard('customer')->user();

        $result = $this->accountService->updateProfile($customer->id, $request->only([
            'first_name',
            'last_name',
            'email',
            'phone',
        ]));

        if ($result) {
            return redirect()->route('shop.customer.account.index')
                ->with('success', 'Profil berhasil diperbarui');
        }

        return redirect()->back()
            ->with('error', 'Gagal memperbarui profil');
    }

    /**
     * Display addresses.
     */
    public function addresses()
    {
        $customer = auth()->guard('customer')->user();
        $addresses = $this->accountService->getAddresses($customer->id);

        return view('beres-account::account.addresses', [
            'customer'  => $customer,
            'addresses' => $addresses,
        ]);
    }

    /**
     * Add new address.
     */
    public function addAddress(Request $request)
    {
        $request->validate([
            'address1'   => 'required|string|max:255',
            'city'       => 'required|string|max:255',
            'state'      => 'required|string|max:255',
            'postcode'   => 'required|string|max:10',
            'country'    => 'required|string|max:2',
            'phone'      => 'required|string|max:20',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
        ]);

        $customer = auth()->guard('customer')->user();

        $result = $this->accountService->addAddress($customer->id, $request->all());

        if ($result) {
            return redirect()->route('shop.customer.account.addresses')
                ->with('success', 'Alamat berhasil ditambahkan');
        }

        return redirect()->back()
            ->with('error', 'Gagal menambahkan alamat');
    }

    /**
     * Update address.
     */
    public function updateAddress(Request $request, $addressId)
    {
        $request->validate([
            'address1'   => 'required|string|max:255',
            'city'       => 'required|string|max:255',
            'state'      => 'required|string|max:255',
            'postcode'   => 'required|string|max:10',
            'country'    => 'required|string|max:2',
            'phone'      => 'required|string|max:20',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
        ]);

        $customer = auth()->guard('customer')->user();

        $result = $this->accountService->updateAddress($customer->id, $addressId, $request->all());

        if ($result) {
            return redirect()->route('shop.customer.account.addresses')
                ->with('success', 'Alamat berhasil diperbarui');
        }

        return redirect()->back()
            ->with('error', 'Gagal memperbarui alamat');
    }

    /**
     * Delete address.
     */
    public function deleteAddress($addressId)
    {
        $customer = auth()->guard('customer')->user();

        $result = $this->accountService->deleteAddress($customer->id, $addressId);

        if ($result) {
            return redirect()->route('shop.customer.account.addresses')
                ->with('success', 'Alamat berhasil dihapus');
        }

        return redirect()->back()
            ->with('error', 'Gagal menghapus alamat');
    }

    /**
     * Display order history.
     */
    public function orders()
    {
        $customer = auth()->guard('customer')->user();
        $orders = $this->accountService->getOrderHistory($customer->id);

        return view('beres-account::account.orders', [
            'customer' => $customer,
            'orders'   => $orders,
        ]);
    }

    /**
     * Display order detail.
     */
    public function orderDetail($orderId)
    {
        $customer = auth()->guard('customer')->user();
        $order = $this->accountService->getOrderDetail($customer->id, $orderId);

        if (!$order) {
            return redirect()->route('shop.customer.account.orders')
                ->with('error', 'Pesanan tidak ditemukan');
        }

        return view('beres-account::account.order-detail', [
            'customer' => $customer,
            'order'    => $order,
        ]);
    }

    /**
     * Display wishlist.
     */
    public function wishlist()
    {
        $customer = auth()->guard('customer')->user();
        $wishlist = $this->accountService->getWishlist($customer->id);

        return view('beres-account::account.wishlist', [
            'customer'  => $customer,
            'wishlist'  => $wishlist,
        ]);
    }

    /**
     * Add to wishlist.
     */
    public function addToWishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $customer = auth()->guard('customer')->user();

        $result = $this->accountService->addToWishlist($customer->id, $request->input('product_id'));

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Produk ditambahkan ke wishlist',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Produk sudah ada di wishlist',
        ], 400);
    }

    /**
     * Remove from wishlist.
     */
    public function removeFromWishlist($productId)
    {
        $customer = auth()->guard('customer')->user();

        $result = $this->accountService->removeFromWishlist($customer->id, $productId);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Produk dihapus dari wishlist',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus dari wishlist',
        ], 400);
    }

    /**
     * Display change password form.
     */
    public function changePassword()
    {
        $customer = auth()->guard('customer')->user();

        return view('beres-account::account.change-password', [
            'customer' => $customer,
        ]);
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $customer = auth()->guard('customer')->user();

        $result = $this->accountService->changePassword(
            $customer->id,
            $request->input('current_password'),
            $request->input('password')
        );

        if ($result) {
            return redirect()->route('shop.customer.account.index')
                ->with('success', 'Password berhasil diperbarui');
        }

        return redirect()->back()
            ->with('error', 'Password lama salah');
    }
}
