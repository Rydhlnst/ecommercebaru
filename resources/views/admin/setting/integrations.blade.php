@extends('layouts.admin')

@section('title', 'Pembayaran & Pengiriman (API)')
@section('page-title', 'Pembayaran & Pengiriman (API)')

@section('admin_content')
<div class="page-header">
    <h1>Integrasi Pembayaran & Ongkos Kirim</h1>
</div>

<form method="POST" action="{{ route('admin.settings.integrations.update') }}">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Midtrans Payment Gateway --}}
        <div class="admin-panel-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-credit-card mr-2 text-blue-600"></i> Midtrans Payment Gateway
                </h3>
                <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ ($settings['midtrans_is_active'] ?? '1') == '1' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ ($settings['midtrans_is_active'] ?? '1') == '1' ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            <p class="text-xs text-gray-500 mb-5">
                Mendukung pembayaran otomatis: QRIS, GoPay, ShopeePay, Virtual Account (BCA, Mandiri, BNI, BRI), Kartu Kredit, dan Minimarket.
            </p>

            <div class="mb-4">
                <label class="form-label">Status Gateway</label>
                <select name="midtrans_is_active" class="form-input">
                    <option value="1" {{ old('midtrans_is_active', $settings['midtrans_is_active'] ?? '1') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('midtrans_is_active', $settings['midtrans_is_active'] ?? '1') == '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Environment / Mode</label>
                <select name="midtrans_environment" class="form-input">
                    <option value="sandbox" {{ old('midtrans_environment', $settings['midtrans_environment'] ?? 'sandbox') == 'sandbox' ? 'selected' : '' }}>Sandbox (Uji Coba)</option>
                    <option value="production" {{ old('midtrans_environment', $settings['midtrans_environment'] ?? 'sandbox') == 'production' ? 'selected' : '' }}>Production (Live/Aktif)</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Server Key</label>
                <input type="text" name="midtrans_server_key" value="{{ old('midtrans_server_key', $settings['midtrans_server_key'] ?? '') }}" class="form-input" placeholder="SB-Mid-server-xxxxxxxxx / Mid-server-xxxxxxxxx">
            </div>

            <div class="mb-4">
                <label class="form-label">Client Key</label>
                <input type="text" name="midtrans_client_key" value="{{ old('midtrans_client_key', $settings['midtrans_client_key'] ?? '') }}" class="form-input" placeholder="SB-Mid-client-xxxxxxxxx / Mid-client-xxxxxxxxx">
            </div>

            <div class="mb-4">
                <label class="form-label">Merchant ID</label>
                <input type="text" name="midtrans_merchant_id" value="{{ old('midtrans_merchant_id', $settings['midtrans_merchant_id'] ?? '') }}" class="form-input" placeholder="Gxxxxxxxxx">
            </div>
        </div>

        {{-- RajaOngkir Shipping Gateway --}}
        <div class="admin-panel-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-truck mr-2 text-green-600"></i> RajaOngkir (Kalkulator Ongkir)
                </h3>
                <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ ($settings['rajaongkir_is_active'] ?? '0') == '1' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ ($settings['rajaongkir_is_active'] ?? '0') == '1' ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            <p class="text-xs text-gray-500 mb-5">
                Menggunakan RajaOngkir/Komerce API V2 untuk tarif ongkos kirim real-time. API key hanya dipakai di server.
            </p>

            <div class="mb-4">
                <label class="form-label">Status Ongkir Otomatis</label>
                <select name="rajaongkir_is_active" class="form-input">
                    <option value="1" {{ old('rajaongkir_is_active', $settings['rajaongkir_is_active'] ?? '0') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('rajaongkir_is_active', $settings['rajaongkir_is_active'] ?? '0') == '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">Tipe Akun RajaOngkir</label>
                <select name="rajaongkir_api_type" class="form-input">
                    <option value="starter" {{ old('rajaongkir_api_type', $settings['rajaongkir_api_type'] ?? 'starter') == 'starter' ? 'selected' : '' }}>Starter (Gratis)</option>
                    <option value="basic" {{ old('rajaongkir_api_type', $settings['rajaongkir_api_type'] ?? 'starter') == 'basic' ? 'selected' : '' }}>Basic</option>
                    <option value="pro" {{ old('rajaongkir_api_type', $settings['rajaongkir_api_type'] ?? 'starter') == 'pro' ? 'selected' : '' }}>Pro</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label">API Key RajaOngkir</label>
                <input type="text" name="rajaongkir_api_key" value="{{ old('rajaongkir_api_key', $settings['rajaongkir_api_key'] ?? '') }}" class="form-input" placeholder="Masukkan API Key RajaOngkir">
            </div>

            <div class="mb-4">
                <label class="form-label">ID Kota Asal Pengiriman</label>
                <input type="text" name="rajaongkir_origin_city" value="{{ old('rajaongkir_origin_city', $settings['rajaongkir_origin_city'] ?? '152') }}" class="form-input" placeholder="Contoh: 152 (Jakarta Pusat), 501 (Bandung), 444 (Surabaya)">
                <p class="text-xs text-gray-400 mt-1">Isi dengan ID Kota pengiriman barang toko Anda dari RajaOngkir.</p>
            </div>

            <div class="mb-4">
                <label class="form-label">Kurir yang Ditampilkan</label>
                <input type="text" name="rajaongkir_couriers" value="{{ old('rajaongkir_couriers', $settings['rajaongkir_couriers'] ?? 'jne,jnt,sicepat,anteraja') }}" class="form-input" placeholder="jne,jnt,sicepat,anteraja">
                <p class="text-xs text-gray-400 mt-1">Pisahkan dengan koma. Contoh: jne,jnt,sicepat,anteraja,ninja,lion.</p>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" class="btn-primary">
            <i class="fas fa-save mr-1"></i> Simpan Pengaturan Integrasi
        </button>
    </div>
</form>
@endsection
