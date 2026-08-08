@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('admin_content')
<div class="page-header">
    <h1>Dashboard</h1>
    <span class="text-sm text-gray-500">{{ now()->format('d M Y') }}</span>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="stats-card">
        <div class="flex items-center justify-between">
            <div>
                <div class="stats-value">{{ number_format($totalOrders) }}</div>
                <div class="stats-label">Total Pesanan</div>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-shopping-cart text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="stats-card">
        <div class="flex items-center justify-between">
            <div>
                <div class="stats-value">{{ number_format($totalProducts) }}</div>
                <div class="stats-label">Jumlah Produk</div>
            </div>
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-box text-green-500 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="stats-card">
        <div class="flex items-center justify-between">
            <div>
                <div class="stats-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="stats-label">Total Pendapatan (Completed)</div>
            </div>
            <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-yellow-500 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="admin-panel-card">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-900">Pesanan Terbaru</h3>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
    </div>

    @if($recentOrders->isEmpty())
        <p class="text-gray-500 text-sm py-4 text-center">Belum ada pesanan.</p>
    @else
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr>
                        <td class="font-medium">{{ $order->order_number }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $statusClass = match($order->status) {
                                    'pending' => 'admin-badge-pending',
                                    'processing' => 'admin-badge-processing',
                                    'completed' => 'admin-badge-completed',
                                    'cancelled' => 'admin-badge-cancelled',
                                    default => 'admin-badge-pending'
                                };
                            @endphp
                            <span class="admin-badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td>
                            <span class="admin-badge {{ $order->payment_status === 'paid' ? 'admin-badge-paid' : 'admin-badge-pending' }}">
                                {{ $order->payment_status === 'paid' ? 'Dibayar' : 'Pending' }}
                            </span>
                        </td>
                        <td class="text-gray-500 text-sm">{{ $order->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
