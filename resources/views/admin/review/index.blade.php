@extends('layouts.admin')

@section('title', 'Management Komentar')
@section('page-title', 'Management Komentar')

@section('admin_content')
<div class="page-header">
    <h1>Management Komentar</h1>
</div>

<div class="admin-panel-card">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>Produk</th>
                    <th>Rating</th>
                    <th>Komentar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                <tr>
                    <td class="font-medium">{{ $review->customer_name }}</td>
                    <td class="text-sm">{{ $review->product->name ?? '-' }}</td>
                    <td>
                        <div class="star-rating">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                    </td>
                    <td class="text-sm max-w-[250px] truncate">{{ $review->comment ?? '-' }}</td>
                    <td>
                        @if($review->is_approved)
                            <span class="admin-badge admin-badge-completed">Disetujui</span>
                        @else
                            <span class="admin-badge admin-badge-pending">Pending</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex items-center gap-2">
                            @if(!$review->is_approved)
                                <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-primary btn-sm" title="Setujui">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirmDelete(this)">
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
                    <td colspan="6" class="text-center py-8 text-gray-500">
                        <i class="fas fa-comments text-3xl mb-2 block"></i>
                        Belum ada komentar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $reviews->links() }}
    </div>
</div>
@endsection

@section('scripts')
function confirmDelete(form) {
    event.preventDefault();
    Swal.fire({
        title: 'Hapus Komentar?',
        text: 'Komentar yang dihapus tidak dapat dikembalikan.',
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
