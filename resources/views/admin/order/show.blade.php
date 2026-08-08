@extends('layouts.admin')

@section('title', 'Detail Pesanan')
@section('page-title', 'Detail Pesanan #' . $order->order_number)

@section('admin_content')
<div class="page-header">
    <h1>Pesanan #{{ $order->order_number }}</h1>
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-4">Informasi Pengiriman</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Nama Pelanggan</span>
                    <p class="font-medium">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Telepon</span>
                    <p class="font-medium">{{ $order->customer_phone ?? '-' }}</p>
                </div>
                <div class="sm:col-span-2">
                    <span class="text-gray-500">Alamat Pengiriman</span>
                    <p class="font-medium">{{ $order->shipping_address ?? $order->customer_address ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Kurir</span>
                    <p class="font-medium">{{ $order->shipping_courier ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Layanan</span>
                    <p class="font-medium">{{ $order->shipping_service ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-4">Item Pesanan</h3>
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td class="font-medium">{{ $item->product_name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="font-medium">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-4">Ringkasan Bayar</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Subtotal</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Ongkir</span>
                    <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                <div class="border-t pt-2 mt-2 flex justify-between font-semibold text-lg">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-4">Update Status</h3>
            <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Status Pesanan</label>
                    <select name="status" class="form-input">
                        @foreach(['pending', 'processing', 'completed', 'cancelled'] as $s)
                            <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Status Pembayaran</label>
                    <select name="payment_status" class="form-input">
                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary w-full">
                    <i class="fas fa-save mr-1"></i> Update Status
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
