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

        {{-- Category Type Selector --}}
        <div class="mb-5">
            <label class="form-label font-semibold text-gray-800">Tipe Kategori <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
                <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer hover:border-blue-500 transition-all {{ !old('parent_id') ? 'border-blue-600 bg-blue-50/50' : 'border-gray-200' }}" id="label-type-root">
                    <div class="flex items-center gap-2 mb-1">
                        <input type="radio" name="category_type" value="root" {{ !old('parent_id') ? 'checked' : '' }} onchange="toggleCategoryType('root')" class="text-blue-600 focus:ring-blue-500">
                        <span class="font-semibold text-gray-900 text-sm">📁 Kategori Induk (Utama)</span>
                    </div>
                    <span class="text-xs text-gray-500 pl-5">Kategori level 1 (misal: Makanan, Minuman, Sayuran). Tampil di navbar beranda.</span>
                </label>

                <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer hover:border-blue-500 transition-all {{ old('parent_id') ? 'border-blue-600 bg-blue-50/50' : 'border-gray-200' }}" id="label-type-sub">
                    <div class="flex items-center gap-2 mb-1">
                        <input type="radio" name="category_type" value="sub" {{ old('parent_id') ? 'checked' : '' }} onchange="toggleCategoryType('sub')" class="text-blue-600 focus:ring-blue-500">
                        <span class="font-semibold text-gray-900 text-sm">📂 Sub-Kategori (Turunan)</span>
                    </div>
                    <span class="text-xs text-gray-500 pl-5">Cabang dari kategori induk (misal: Garam Himalaya di bawah Makanan).</span>
                </label>
            </div>
        </div>

        {{-- Category Name --}}
        <div class="mb-4">
            <label class="form-label">Nama Kategori <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Makanan, Sayuran, atau Garam Himalaya" class="form-input">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Parent Category Dropdown (shown only when Sub-Kategori is selected) --}}
        <div id="parent-category-group" class="mb-4 {{ old('parent_id') ? '' : 'hidden' }}">
            <label class="form-label">Pilih Kategori Induk <span class="text-red-500">*</span></label>
            @if($parentCategories->isEmpty())
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-xs flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Belum ada Kategori Induk di database. Silakan pilih <strong>"Kategori Induk (Utama)"</strong> terlebih dahulu untuk membuat kategori pertama.</span>
                </div>
            @else
                <select name="parent_id" id="parent-id-select" class="form-input">
                    <option value="">— Pilih Kategori Induk —</option>
                    @foreach($parentCategories as $cat)
                        <option value="{{ $cat->id }}" {{ old('parent_id') == $cat->id ? 'selected' : '' }}>
                            📁 {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            @endif
        </div>

        {{-- Image Upload with locked 1:1 Cropper --}}
        <div class="mb-6">
            <label class="form-label">Gambar Kategori (Opsional)</label>
            <input type="file" name="image" id="category-image-input" accept="image/*" class="form-input" onchange="handleCategoryFileInput(this)">
            <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, WEBP (maks. 10MB). Crop otomatis 1:1 untuk gambar kategori.</p>
            <div id="image-preview" class="mt-2 hidden">
                <img src="" alt="Preview" class="w-32 h-32 object-cover rounded-lg border">
            </div>
            @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save mr-1"></i> Simpan Kategori
            </button>
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Batal</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
function toggleCategoryType(type) {
    const parentGroup = document.getElementById('parent-category-group');
    const parentSelect = document.getElementById('parent-id-select');
    const labelRoot = document.getElementById('label-type-root');
    const labelSub = document.getElementById('label-type-sub');

    if (type === 'root') {
        parentGroup.classList.add('hidden');
        if (parentSelect) parentSelect.value = '';
        labelRoot.classList.add('border-blue-600', 'bg-blue-50/50');
        labelRoot.classList.remove('border-gray-200');
        labelSub.classList.remove('border-blue-600', 'bg-blue-50/50');
        labelSub.classList.add('border-gray-200');
    } else {
        parentGroup.classList.remove('hidden');
        labelSub.classList.add('border-blue-600', 'bg-blue-50/50');
        labelSub.classList.remove('border-gray-200');
        labelRoot.classList.remove('border-blue-600', 'bg-blue-50/50');
        labelRoot.classList.add('border-gray-200');
    }
}

function handleCategoryFileInput(input) {
    if (input.files && input.files[0]) {
        window.AdminCropper.initForInput(input, {
            aspectRatio: 1,
            ratioLabel: '1:1 (Kategori)',
            onComplete: function(inputEl) {
                previewImage(inputEl);
            }
        });
    }
}

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
