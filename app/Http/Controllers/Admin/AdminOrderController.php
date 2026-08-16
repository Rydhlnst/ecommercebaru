<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminOrder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = new LengthAwarePaginator(collect(), 0, 15, 1);

        try {
            if (Schema::hasTable('admin_orders')) {
                $orders = AdminOrder::latest()->paginate(15);
            }
        } catch (QueryException $e) {
            // Table might not exist yet
        }

        return view('admin.order.index', compact('orders'));
    }

    public function show($id)
    {
        $order = AdminOrder::findOrFail($id);
        $order->load('items.product');

        return view('admin.order.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = AdminOrder::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'payment_status' => 'required|in:pending,paid',
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order->id)->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $order = AdminOrder::findOrFail($id);

        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }
}
