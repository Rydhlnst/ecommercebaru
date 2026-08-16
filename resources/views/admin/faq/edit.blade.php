@extends('layouts.admin')

@section('title', 'Edit FAQ')
@section('page-title', 'Edit FAQ')

@section('admin_content')
<div class="page-header">
    <h1>Edit FAQ</h1>
    <a href="{{ route('admin.faqs.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<div class="admin-panel-card max-w-2xl">
    <form method="POST" action="{{ route('admin.faqs.update', $faq->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="form-label">Pertanyaan <span class="text-red-500">*</span></label>
            <input type="text" name="question" value="{{ old('question', $faq->question) }}" required class="form-input">
            @error('question') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Jawaban <span class="text-red-500">*</span></label>
            <textarea name="answer" rows="6" class="form-input" required>{{ old('answer', $faq->answer) }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Mendukung HTML.</p>
            @error('answer') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $faq->is_active) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm font-medium text-gray-700">Aktif</span>
                </label>
            </div>
            <div>
                <label class="form-label">Urutan</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $faq->sort_order) }}" min="0" class="form-input">
            </div>
        </div>

        <button type="submit" class="btn-primary">
            <i class="fas fa-save mr-1"></i> Perbarui FAQ
        </button>
    </form>
</div>
@endsection
