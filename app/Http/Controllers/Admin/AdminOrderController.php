<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminOrder;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = AdminOrder::latest()->paginate(15);

        return view('admin.order.index', compact('orders'));
    }

    public function show(AdminOrder $order)
    {
        $order->load('items.product');

        return view('admin.order.show', compact('order'));
    }

    public function updateStatus(Request $request, AdminOrder $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'payment_status' => 'required|in:pending,paid',
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function destroy(AdminOrder $order)
    {
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }
}
