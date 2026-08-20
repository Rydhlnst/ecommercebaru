@extends('layouts.admin')

@section('title', 'Kelola Produk')
@section('page-title', 'Kelola Produk')

@section('admin_content')
<div class="page-header">
    <h1>Kelola Produk</h1>
    <div class="flex items-center gap-3">
        @if($products->total() > 0)
            <form action="{{ route('admin.products.clearAll') }}" method="POST" onsubmit="return confirmClearAllProducts(this)">
                @csrf
                <button type="submit" class="btn-danger">
                    <i class="fas fa-trash-alt mr-1"></i> Kosongkan Semua Produk
                </button>
            </form>
        @endif
        <a href="{{ route('admin.products.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-1"></i> Tambah Produk
        </a>
    </div>
</div>

<div class="admin-panel-card">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Badge</th>
                    <th>Status</th>
                    <th>Featured</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        @if($product->images->count())
                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-contain p-1">
                        @else
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                    </td>
                    <td class="font-medium">{{ $product->name }}</td>
                    <td class="text-sm text-gray-500">{{ $product->category->name ?? '-' }}</td>
                    <td class="font-medium">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>
                        @if($product->stock <= 0)
                            <span class="text-red-500 font-medium">0</span>
                        @elseif($product->stock <= 5)
                            <span class="text-yellow-500 font-medium">{{ $product->stock }}</span>
                        @else
                            <span class="text-green-600 font-medium">{{ $product->stock }}</span>
                        @endif
                    </td>
                    <td>
                        @if($product->badge)
                            <span class="admin-badge admin-badge-{{ $product->badge }}">
                                {{ $product->badge === 'new' ? 'Baru' : ($product->badge === 'sale' ? 'Sale' : 'Habis Terjual') }}
                            </span>
                        @else
                            <span class="text-gray-400 text-sm">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="admin-badge {{ ($product->status ?? 'active') === 'active' ? 'admin-badge-active' : 'admin-badge-inactive' }}">
                            {{ ($product->status ?? 'active') === 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        @if($product->is_featured)
                            <i class="fas fa-star text-yellow-400"></i>
                        @else
                            <i class="far fa-star text-gray-300"></i>
                        @endif
                    </td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirmDelete(this)">
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
                    <td colspan="9" class="text-center py-8 text-gray-500">
                        <i class="fas fa-box-open text-3xl mb-2 block"></i>
                        Belum ada produk.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection

@section('scripts')
function confirmDelete(form) {
    event.preventDefault();
    Swal.fire({
        title: 'Hapus Produk?',
        text: 'Data yang dihapus tidak dapat dikembalikan.',
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

function confirmClearAllProducts(form) {
    event.preventDefault();
    Swal.fire({
        title: 'Kosongkan Semua Produk?',
        text: 'PERINGATAN: Seluruh produk dan gambar di database akan dihapus bersih!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Kosongkan Semua',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}
@endsection
