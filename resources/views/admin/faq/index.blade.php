@extends('layouts.admin')

@section('title', 'FAQ Management')
@section('page-title', 'FAQ Management')

@section('admin_content')
<div class="page-header">
    <h1>FAQ Management</h1>
    <a href="{{ route('admin.faqs.create') }}" class="btn-primary">
        <i class="fas fa-plus mr-1"></i> Tambah FAQ
    </a>
</div>

<div class="admin-panel-card">
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Pertanyaan</th>
                    <th>Jawaban</th>
                    <th>Status</th>
                    <th>Urutan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($faqs as $faq)
                <tr>
                    <td class="font-medium max-w-[250px]">{{ $faq->question }}</td>
                    <td class="text-sm text-gray-600 max-w-[300px] truncate">{!! Str::limit(strip_tags($faq->answer), 50) !!}</td>
                    <td>
                        <span class="admin-badge {{ $faq->is_active ? 'admin-badge-active' : 'admin-badge-inactive' }}">
                            {{ $faq->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </td>
                    <td>{{ $faq->sort_order }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" onsubmit="return confirmDelete(this)">
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
                    <td colspan="5" class="text-center py-8 text-gray-500">
                        <i class="fas fa-question-circle text-3xl mb-2 block"></i>
                        Belum ada FAQ.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $faqs->links() }}
    </div>
</div>
@endsection

@section('scripts')
function confirmDelete(form) {
    event.preventDefault();
    Swal.fire({
        title: 'Hapus FAQ?',
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
