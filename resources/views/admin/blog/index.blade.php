@extends('layouts.admin')

@section('title', 'Management Blog')
@section('page-title', 'Management Blog')

@section('admin_content')
<div class="page-header">
    <h1>Management Blog</h1>
    <a href="{{ route('admin.blog.create') }}" class="btn-primary">
        <i class="fas fa-plus mr-1"></i> Tambah Postingan
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-4">Daftar Postingan</h3>
            <div class="overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Thumbnail</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Tags</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                        <tr>
                            <td>
                                @if($post->thumbnail)
                                    <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->title }}" class="w-12 h-12 rounded-lg object-cover">
                                @else
                                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="font-medium max-w-[200px] truncate">{{ $post->title }}</td>
                            <td class="text-sm text-gray-500">{{ $post->category->name ?? '-' }}</td>
                            <td>
                                @if($post->tags)
                                    @foreach(explode(',', $post->tags) as $tag)
                                        <span class="inline-block bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded-full">#{{ trim($tag) }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if($post->is_published)
                                    <span class="admin-badge admin-badge-completed">Terbit</span>
                                @else
                                    <span class="admin-badge admin-badge-inactive">Draft</span>
                                @endif
                            </td>
                            <td class="text-gray-500 text-sm">{{ $post->published_at ? $post->published_at->format('d M Y') : '-' }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.blog.edit', $post->id) }}" class="btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.blog.destroy', $post->id) }}" method="POST" onsubmit="return confirmDelete(this)">
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
                                <i class="fas fa-blog text-3xl mb-2 block"></i>
                                Belum ada postingan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $posts->links() }}</div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-4">Kategori Blog</h3>
            <form method="POST" action="{{ route('admin.blog.storeCategory') }}" class="flex gap-2 mb-4" id="category-form">
                @csrf
                <input type="text" name="name" placeholder="Nama kategori baru..." class="form-input flex-1" required id="category-name-input">
                <button type="submit" class="btn-primary btn-sm"><i class="fas fa-plus"></i></button>
            </form>

            <div id="categories-list" class="space-y-2">
                @foreach($categories as $cat)
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg" id="cat-{{ $cat->id }}">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <span class="category-name text-sm truncate">{{ $cat->name }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button onclick="editCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')" class="text-gray-400 hover:text-blue-600 p-1">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                        <form action="{{ route('admin.blog.destroyCategory', $cat) }}" method="POST" onsubmit="return confirmDelete(this)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600 p-1">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="edit-category-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-sm">
        <h3 class="font-semibold text-gray-900 mb-4">Edit Kategori</h3>
        <form method="POST" id="edit-category-form">
            @csrf
            @method('PUT')
            <input type="text" name="name" id="edit-category-name" class="form-input mb-4" required>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1">Simpan</button>
                <button type="button" onclick="closeEditModal()" class="btn-danger flex-1">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
function confirmDelete(form) {
    event.preventDefault();
    Swal.fire({
        title: 'Hapus?',
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

function editCategory(id, name) {
    document.getElementById('edit-category-form').action = '{{ url("admin/blog/categories") }}/' + id;
    document.getElementById('edit-category-name').value = name;
    document.getElementById('edit-category-modal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('edit-category-modal').classList.add('hidden');
}
@endsection
