@extends('layouts.admin')

@section('title', 'Pengaturan Toko')
@section('page-title', 'Pengaturan Toko')

@section('admin_content')
<div class="page-header">
    <h1>Pengaturan Toko</h1>
</div>

<form method="POST" action="{{ route('admin.settings.store.update') }}">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-store mr-2 text-blue-500"></i>Informasi Toko</h3>

            <div class="mb-4">
                <label class="form-label">Nomor WhatsApp</label>
                <input type="text" name="store_whatsapp" value="{{ old('store_whatsapp', $settings['store_whatsapp'] ?? '') }}" class="form-input" placeholder="6281234567890">
            </div>

            <div class="mb-4">
                <label class="form-label">Link Google Maps Embed</label>
                <textarea name="store_maps_embed" rows="3" class="form-input" placeholder="<iframe src=...">{{ old('store_maps_embed', $settings['store_maps_embed'] ?? '') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label">Negara</label>
                    <input type="text" name="store_country" value="{{ old('store_country', $settings['store_country'] ?? 'Indonesia') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="store_email" value="{{ old('store_email', $settings['store_email'] ?? '') }}" class="form-input">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label">Telepon</label>
                    <input type="text" name="store_phone" value="{{ old('store_phone', $settings['store_phone'] ?? '') }}" class="form-input">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Alamat</label>
                <textarea name="store_address" rows="3" class="form-input">{{ old('store_address', $settings['store_address'] ?? '') }}</textarea>
            </div>
        </div>

        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-shopping-bag mr-2 text-orange-500"></i>Link Marketplace</h3>

            <div class="mb-4">
                <label class="form-label">
                    <i class="fab fa-shopify text-orange-500 mr-1"></i> Shopee
                </label>
                <input type="url" name="store_shopee" value="{{ old('store_shopee', $settings['store_shopee'] ?? '') }}" class="form-input" placeholder="https://shopee.co.id/...">
            </div>

            <div class="mb-4">
                <label class="form-label">
                    <i class="fas fa-store text-green-500 mr-1"></i> Tokopedia
                </label>
                <input type="url" name="store_tokopedia" value="{{ old('store_tokopedia', $settings['store_tokopedia'] ?? '') }}" class="form-input" placeholder="https://tokopedia.com/...">
            </div>

            <div class="mb-4">
                <label class="form-label">
                    <i class="fas fa-shopping-cart text-blue-500 mr-1"></i> Lazada
                </label>
                <input type="url" name="store_lazada" value="{{ old('store_lazada', $settings['store_lazada'] ?? '') }}" class="form-input" placeholder="https://www.lazada.co.id/...">
            </div>

            <div class="mb-4">
                <label class="form-label">
                    <i class="fab fa-tiktok text-gray-800 mr-1"></i> TikTok Shop
                </label>
                <input type="url" name="store_tiktok" value="{{ old('store_tiktok', $settings['store_tiktok'] ?? '') }}" class="form-input" placeholder="https://www.tiktok.com/@...">
            </div>
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" class="btn-primary">
            <i class="fas fa-save mr-1"></i> Simpan Pengaturan
        </button>
    </div>
</form>
@endsection
