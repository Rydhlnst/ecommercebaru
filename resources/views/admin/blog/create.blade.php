@extends('layouts.admin')

@section('title', 'Tambah Postingan')
@section('page-title', 'Tambah Postingan')

@section('admin_content')
<div class="page-header">
    <h1>Tambah Postingan</h1>
    <a href="{{ route('admin.blog.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<form method="POST" action="{{ route('admin.blog.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="admin-panel-card">
                <h3 class="font-semibold text-gray-900 mb-4">Konten</h3>

                <div class="mb-4">
                    <label class="form-label">Judul <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="form-input">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Konten</label>
                    <textarea name="content" id="content-editor" rows="15" class="form-input">{{ old('content') }}</textarea>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="admin-panel-card">
                <h3 class="font-semibold text-gray-900 mb-4">Pengaturan</h3>

                <div class="mb-4">
                    <label class="form-label">Kategori</label>
                    <select name="blog_category_id" class="form-input">
                        <option value="">— Pilih Kategori —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('blog_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label">Thumbnail Artikel (16:9 Banner)</label>
                    <input type="file" name="thumbnail" id="blog-thumb-input" accept="image/*" class="form-input" onchange="handleBlogThumbInput(this)">
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, WEBP (maks. 10MB). Cropper interaktif 16:9 banner akan otomatis terbuka.</p>
                    <div id="thumb-preview" class="mt-2 hidden">
                        <img src="" alt="Preview" class="w-full h-32 object-cover rounded-lg border">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Tags (koma)</label>
                    <input type="text" name="tags" value="{{ old('tags') }}" class="form-input" placeholder="tips, tutorial, berita">
                </div>

                <div class="mb-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                        <span class="text-sm font-medium text-gray-700">Terbitkan</span>
                    </label>
                </div>

                <button type="submit" class="btn-primary w-full">
                    <i class="fas fa-save mr-1"></i> Simpan Postingan
                </button>
            </div>
        </div>
    </div>
</form>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
@endsection

@section('scripts')
function AnkeshUploadAdapter(loader) {
    this.loader = loader;
    this.upload = function() {
        return this.loader.file.then(function(file) {
            return new Promise(function(resolve, reject) {
                const data = new FormData();
                data.append('upload', file);
                fetch('{{ route("admin.blog.uploadImage") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: data
                })
                .then(function(res) { return res.json(); })
                .then(function(json) {
                    if (json.url) {
                        resolve({ default: json.url });
                    } else {
                        reject(json.error || 'Upload gagal');
                    }
                })
                .catch(reject);
            });
        });
    };
    this.abort = function() {};
}
function AnkeshUploadAdapterPlugin(editor) {
    editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
        return new AnkeshUploadAdapter(loader);
    };
}

document.addEventListener('DOMContentLoaded', function() {
    ClassicEditor.create(document.querySelector('#content-editor'), {
        extraPlugins: [AnkeshUploadAdapterPlugin],
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'blockQuote', '|', 'imageUpload', 'insertTable', 'mediaEmbed', '|', 'undo', 'redo'],
        image: {
            upload: {
                types: ['jpeg', 'png', 'webp']
            }
        }
    }).catch(console.error);
});

function handleBlogThumbInput(input) {
    if (input.files && input.files[0]) {
        window.AdminCropper.initForInput(input, {
            aspectRatio: 16/9,
            ratioLabel: '16:9 (Blog)',
            onComplete: function(inputEl) {
                previewThumbnail(inputEl);
            }
        });
    }
}

function previewThumbnail(input) {
    const preview = document.getElementById('thumb-preview');
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
