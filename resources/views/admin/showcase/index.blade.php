@extends('layouts.admin')

@section('title', 'Showcase Produk')
@section('page-title', 'Showcase Produk')

@section('admin_content')
<div class="page-header">
    <h1>Showcase Produk</h1>
</div>

<div class="admin-panel-card max-w-3xl">
    <form method="POST" action="{{ route('admin.showcase.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="form-label">Produk yang Ditampilkan</label>
            <select name="product_id" class="form-input">
                <option value="">— Pilih Produk —</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ ($showcase->product_id ?? '') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }} — Rp {{ number_format($product->price, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label">Gambar Showcase</label>
            @if($showcase && $showcase->image)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $showcase->image) }}" alt="Showcase" class="w-48 h-32 object-cover rounded-lg border">
                </div>
            @endif
            <input type="file" name="image" accept="image/*" class="form-input" onchange="previewImage(this)">
            <p class="text-xs text-gray-400 mt-1">Akan dikompres otomatis ke WebP 800px.</p>
            <div id="image-preview" class="mt-2 hidden">
                <img src="" alt="Preview" class="w-48 h-32 object-cover rounded-lg border">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Judul Parameter</label>
            <input type="text" name="param_name" value="{{ old('param_name', $showcase->title ?? '') }}" class="form-input" placeholder="Contoh: Ingredients">
        </div>

        <div class="mb-6">
            <label class="form-label">Item Parameter (Maks. 8, kosong diabaikan)</label>
            <div class="space-y-2">
                @for($i = 0; $i < 8; $i++)
                    <input type="text" name="param_value[]" value="{{ old('param_value')[$i] ?? ($showcase->items[$i] ?? '') }}" class="form-input" placeholder="Item {{ $i + 1 }}">
                @endfor
            </div>
        </div>

        <button type="submit" class="btn-primary">
            <i class="fas fa-save mr-1"></i> Simpan Showcase
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
