@extends('layouts.admin')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')

@section('admin_content')
<div class="page-header">
    <h1>Edit Produk</h1>
    <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" id="product-form">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="admin-panel-card">
                <h3 class="font-semibold text-gray-900 mb-4">Informasi Produk</h3>

                <div class="mb-4">
                    <label class="form-label">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="form-input">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="form-label">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="form-input">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Badge</label>
                        <select name="badge" class="form-input">
                            <option value="">Tanpa Badge</option>
                            <option value="new" {{ old('badge', $product->badge) === 'new' ? 'selected' : '' }}>Baru (New)</option>
                            <option value="sale" {{ old('badge', $product->badge) === 'sale' ? 'selected' : '' }}>Sale</option>
                            <option value="habis_terjual" {{ old('badge', $product->badge) === 'habis_terjual' ? 'selected' : '' }}>Habis Terjual</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-input">
                            <option value="active" {{ old('status', $product->status ?? 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="4" class="form-input">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                        <span class="text-sm font-medium text-gray-700">Best Seller (Featured)</span>
                    </label>
                </div>
            </div>

            <div class="admin-panel-card">
                <h3 class="font-semibold text-gray-900 mb-4">Foto Produk</h3>
                @if($product->images->count())
                    <div class="grid grid-cols-5 gap-2 mb-3">
                        @foreach($product->images as $img)
                            <div class="relative">
                                <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-24 object-cover rounded-lg border">
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 mb-2">Gambar saat ini. Upload baru akan mengganti semua gambar.</p>
                @endif
                <input type="file" name="images[]" id="product-images-input" accept="image/*" multiple class="form-input" onchange="handleProductFileInput(this)">
                <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, WEBP (maks. 10MB per foto). Cropper interaktif 4:5 (atau 1:1) akan terbuka untuk menyesuaikan setiap foto.</p>
                <div id="image-preview" class="grid grid-cols-5 gap-2 mt-2"></div>
            </div>

            <div class="admin-panel-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">Variasi Produk</h3>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="has_variations" value="1" id="toggle-variations" {{ old('has_variations', $product->has_variations) ? 'checked' : '' }} onchange="toggleVariations()" class="w-4 h-4 text-blue-600 rounded">
                        <span class="text-sm font-medium text-gray-700">Aktifkan Variasi</span>
                    </label>
                </div>

                <div id="variation-section" class="{{ old('has_variations', $product->has_variations) ? '' : 'hidden' }}">
                    <div id="variations-container" class="space-y-3">
                        @if(old('variation_price', $product->variations->pluck('price')->toArray()))
                            @foreach(old('variation_price', $product->variations) as $i => $v)
                                <div class="variation-row flex gap-3 items-end">
                                    <div class="flex-1">
                                        <label class="form-label">Berat (kg)</label>
                                        <input type="number" name="variation_weight[]" step="0.01" value="{{ is_object($v) ? $v->weight : (old('variation_weight')[$i] ?? '') }}" class="form-input" oninput="updateGramHint(this)">
                                        <span class="berat-hint text-xs text-gray-400"></span>
                                    </div>
                                    <div class="flex-1">
                                        <label class="form-label">Harga (Rp)</label>
                                        <input type="number" name="variation_price[]" value="{{ is_object($v) ? $v->price : $v }}" class="form-input">
                                    </div>
                                    <div class="flex-1">
                                        <label class="form-label">Stok</label>
                                        <input type="number" name="variation_stock[]" value="{{ is_object($v) ? $v->stock : (old('variation_stock')[$i] ?? '') }}" class="form-input">
                                    </div>
                                    <button type="button" onclick="removeVariation(this)" class="btn-danger btn-sm mb-0.5"><i class="fas fa-times"></i></button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" onclick="addVariation()" class="mt-3 text-sm text-blue-600 hover:underline">
                        <i class="fas fa-plus mr-1"></i> Tambah Variasi
                    </button>
                </div>

                <div id="global-price-section" class="{{ old('has_variations', $product->has_variations) ? 'hidden' : '' }}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Harga Global <span class="text-red-500">*</span></label>
                            <input type="number" name="price" value="{{ old('price', $product->price) }}" required class="form-input">
                            @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Stok Global <span class="text-red-500">*</span></label>
                            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required class="form-input">
                            @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="admin-panel-card sticky top-20">
                <button type="submit" class="btn-primary w-full">
                    <i class="fas fa-save mr-1"></i> Perbarui Produk
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
@verbatim
function toggleVariations() {
    const checked = document.getElementById('toggle-variations').checked;
    document.getElementById('variation-section').classList.toggle('hidden', !checked);
    document.getElementById('global-price-section').classList.toggle('hidden', checked);
}

function addVariation() {
    const container = document.getElementById('variations-container');
    const row = document.createElement('div');
    row.className = 'variation-row flex gap-3 items-end';
    row.innerHTML = `
        <div class="flex-1">
            <label class="form-label">Berat (kg)</label>
            <input type="number" name="variation_weight[]" step="0.01" class="form-input" oninput="updateGramHint(this)">
            <span class="berat-hint text-xs text-gray-400"></span>
        </div>
        <div class="flex-1">
            <label class="form-label">Harga (Rp)</label>
            <input type="number" name="variation_price[]" class="form-input">
        </div>
        <div class="flex-1">
            <label class="form-label">Stok</label>
            <input type="number" name="variation_stock[]" class="form-input">
        </div>
        <button type="button" onclick="removeVariation(this)" class="btn-danger btn-sm mb-0.5"><i class="fas fa-times"></i></button>
    `;
    container.appendChild(row);
}

function removeVariation(btn) {
    btn.closest('.variation-row').remove();
}

function updateGramHint(input) {
    const span = input.parentElement.querySelector('.berat-hint');
    if (!span) return;
    const val = parseFloat(input.value);
    span.textContent = (!isNaN(val) && val > 0) ? '= ' + Math.round(val * 1000) + 'g' : '';
}

document.querySelectorAll('input[name="variation_weight[]"]').forEach(updateGramHint);

function handleProductFileInput(input) {
    if (input.files && input.files.length > 0) {
        window.AdminCropper.initForInput(input, {
            aspectRatio: 4/5,
            onComplete: function(inputEl) {
                previewImages(inputEl);
            }
        });
    }
}

function previewImages(input) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    const files = Array.from(input.files).slice(0, 5);
    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.innerHTML = `<img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg border">`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}
@endverbatim
@endsection
