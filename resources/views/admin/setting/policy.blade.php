@extends('layouts.admin')

@section('title', 'Privacy & Policy')
@section('page-title', 'Privacy & Policy')

@section('admin_content')
<div class="page-header">
    <h1>Policy Pages</h1>
</div>

<form method="POST" action="{{ route('admin.settings.policy.update') }}">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach ($policyDefinitions as $key => $policy)
            <div class="admin-panel-card">
                <h3 class="font-semibold text-gray-900 mb-4"><i class="{{ $policy['icon'] }} mr-2 {{ $policy['icon_class'] }}"></i>{{ $policy['title'] }}</h3>
                <textarea name="{{ $key }}" rows="10" class="form-input font-mono text-sm" required>{{ old($key, $policies[$key] ?? '') }}</textarea>
                @error($key) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-400 mt-1">Safe HTML is supported. Changes are published to <code>/page/{{ $policy['url_key'] }}</code>.</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        <button type="submit" class="btn-primary">
            <i class="fas fa-save mr-1"></i> Simpan Semua Kebijakan
        </button>
    </div>
</form>
@endsection
