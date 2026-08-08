@extends('layouts.admin')

@section('title', 'Kelola Kategori')
@section('page-title', 'Kelola Kategori')

@section('admin_content')
<div class="page-header">
    <h1>Kelola Kategori</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn-primary">
        <i class="fas fa-plus mr-1"></i> Tambah Kategori
    </a>
</div>

<div class="admin-panel-card">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Slug</th>
                    <th>Parent</th>
                    <th>Jumlah Produk</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td>
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-10 h-10 rounded-lg object-cover">
                        @else
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                    </td>
                    <td class="font-medium">{{ $category->name }}</td>
                    <td class="text-gray-500 text-sm">{{ $category->slug }}</td>
                    <td>
                        @if($category->parent)
                            <span class="admin-badge admin-badge-processing">Sub dari {{ $category->parent->name }}</span>
                        @else
                            <span class="admin-badge admin-badge-completed">Induk (Root)</span>
                        @endif
                    </td>
                    <td>{{ $category->products_count ?? $category->products()->count() }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirmDelete(this)">
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
                        <i class="fas fa-folder-open text-3xl mb-2 block"></i>
                        Belum ada kategori.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</div>
@endsection

@section('scripts')
function confirmDelete(form) {
    event.preventDefault();
    Swal.fire({
        title: 'Hapus Kategori?',
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
@endsection
