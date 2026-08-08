@extends('layouts.admin')

@section('title', 'Privacy & Policy')
@section('page-title', 'Privacy & Policy')

@section('admin_content')
<div class="page-header">
    <h1>Privacy & Policy</h1>
</div>

<form method="POST" action="{{ route('admin.settings.policy.update') }}">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-shield-alt mr-2 text-blue-500"></i>Privacy Policy</h3>
            <textarea name="policy_privacy" rows="10" class="form-input font-mono text-sm">{{ old('policy_privacy', $policies['policy_privacy'] ?? '') }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Mendukung HTML.</p>
        </div>

        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-undo mr-2 text-yellow-500"></i>Refund Policy</h3>
            <textarea name="policy_refund" rows="10" class="form-input font-mono text-sm">{{ old('policy_refund', $policies['policy_refund'] ?? '') }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Mendukung HTML.</p>
        </div>

        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-truck mr-2 text-green-500"></i>Shipping Policy</h3>
            <textarea name="policy_shipping" rows="10" class="form-input font-mono text-sm">{{ old('policy_shipping', $policies['policy_shipping'] ?? '') }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Mendukung HTML.</p>
        </div>

        <div class="admin-panel-card">
            <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-file-contract mr-2 text-purple-500"></i>Terms of Service</h3>
            <textarea name="policy_terms" rows="10" class="form-input font-mono text-sm">{{ old('policy_terms', $policies['policy_terms'] ?? '') }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Mendukung HTML.</p>
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" class="btn-primary">
            <i class="fas fa-save mr-1"></i> Simpan Semua Kebijakan
        </button>
    </div>
</form>
@endsection
