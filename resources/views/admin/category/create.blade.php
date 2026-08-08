@extends('layouts.admin')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

@section('admin_content')
<div class="page-header">
    <h1>Tambah Kategori</h1>
    <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<div class="admin-panel-card max-w-2xl">
    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="form-label">Nama Kategori <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required class="form-input">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Parent Kategori</label>
            <select name="parent_id" class="form-input">
                <option value="">— Tidak ada (Root) —</option>
                @foreach($parentCategories as $cat)
                    <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            @error('parent_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="form-label">Gambar Kategori</label>
            <input type="file" name="image" accept="image/*" class="form-input" onchange="previewImage(this)">
            <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG. Akan dikompres otomatis ke WebP 500px.</p>
            <div id="image-preview" class="mt-2 hidden">
                <img src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border">
            </div>
            @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-primary">
            <i class="fas fa-save mr-1"></i> Simpan Kategori
        </button>
    </form>
</div>
@endsection

@section('scripts')
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
@endsection
