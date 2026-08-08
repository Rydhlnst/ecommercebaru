@extends('layouts.admin')

@section('title', 'Kelola Pesanan')
@section('page-title', 'Kelola Pesanan')

@section('admin_content')
<div class="page-header">
    <h1>Kelola Pesanan</h1>
</div>

<div class="admin-panel-card">
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
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="font-medium">{{ $order->order_number }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td class="font-medium">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
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
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn-primary btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirmDelete(this)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-500">
                        <i class="fas fa-shopping-cart text-3xl mb-2 block"></i>
                        Belum ada pesanan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection

@section('scripts')
function confirmDelete(form) {
    event.preventDefault();
    Swal.fire({
        title: 'Hapus Pesanan?',
        text: 'Item pesanan akan dihapus terlebih dahulu.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}
@endsection
