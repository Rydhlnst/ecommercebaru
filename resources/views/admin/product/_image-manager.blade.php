@php
    $imageProduct = $product ?? null;
    $imageDrafts = $imageProduct?->images->map(fn ($image) => [
        'id' => $image->id,
        'url' => $image->detail_url,
        'fit_mode' => $image->fit_mode,
        'focal_x' => $image->focal_x,
        'focal_y' => $image->focal_y,
        'alt_text' => $image->alt_text,
    ])->values() ?? collect();
@endphp

<div class="admin-panel-card" id="product-image-manager">
    <div class="flex items-center justify-between gap-3 mb-4">
        <div>
            <h3 class="font-semibold text-gray-900">Foto Produk</h3>
            <p class="text-xs text-gray-500 mt-1">Upload foto lalu lihat hasilnya seperti di katalog.</p>
        </div>
        <span class="text-xs text-[#2D5A27] bg-[#F5F9F3] border border-[#E8F0E5] rounded-full px-3 py-1">Maks. 5 foto</span>
    </div>

    <input type="file" name="images[]" id="product-images-input" accept="image/jpeg,image/png,image/webp,image/avif" multiple class="sr-only">
    <input type="hidden" name="image_meta" id="product-image-meta" value="[]">
    <div id="remove-image-inputs"></div>

    <label for="product-images-input" id="product-upload-dropzone" class="flex min-h-36 cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-[#C8DBBE] bg-[#FBFDF9] px-5 py-6 text-center transition-colors hover:border-[#2D5A27] hover:bg-[#F5F9F3]">
        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[#E8F0E5] text-[#2D5A27]"><i class="fas fa-cloud-arrow-up"></i></span>
        <span class="mt-3 text-sm font-semibold text-[#171717]">Upload Foto Produk</span>
        <span class="mt-1 text-xs text-[#737373]">Drag & drop foto di sini atau pilih / ganti foto</span>
        <span class="mt-2 text-[11px] text-[#737373]">JPG, PNG, WebP, AVIF · Maks. 10 MB per foto</span>
    </label>

    <p id="product-image-message" class="mt-3 hidden rounded-xl border px-3 py-2 text-xs" role="status"></p>
    <div id="product-image-list" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3"></div>

    <div class="mt-6 border-t border-[#E8F0E5] pt-5">
        <p class="mb-3 text-sm font-semibold text-[#171717]">Preview di Katalog</p>
        <div class="max-w-[260px] rounded-2xl border border-[#E8F0E5] bg-white p-2 shadow-sm">
            <div class="relative aspect-[4/5] overflow-hidden rounded-xl bg-[#F5F9F3]">
                <img id="catalog-preview-image" src="" alt="Preview produk" class="absolute inset-0 h-full w-full object-cover transition-all duration-200">
                <span id="catalog-preview-placeholder" class="absolute inset-0 flex items-center justify-center px-5 text-center text-xs text-[#9AB18F]">Upload foto untuk melihat preview katalog</span>
            </div>
            <div class="p-3">
                <p id="catalog-preview-category" class="text-[10px] font-bold uppercase tracking-wider text-[#2D5A27]"></p>
                <h4 id="catalog-preview-name" class="mt-1 line-clamp-2 min-h-[42px] text-sm font-semibold text-[#171717]">Nama Produk</h4>
                <p id="catalog-preview-price" class="mt-2 text-base font-bold text-[#171717]">Rp 0</p>
            </div>
        </div>
        <p class="mt-3 text-xs text-[#2D5A27]"><i class="fas fa-check mr-1"></i>Foto sudah siap digunakan setelah Anda menyimpan produk.</p>
    </div>

    <div class="mt-6 border-t border-[#E8F0E5] pt-5">
        <p class="mb-1 text-sm font-semibold text-[#171717]">Preview Highlighted Product</p>
        <p class="mb-3 text-xs text-[#737373]">Preview ini mengikuti tampilan produk unggulan di bawah hero section.</p>
        <div class="max-w-[520px] overflow-hidden rounded-2xl border border-[#E8F0E5] bg-white shadow-sm sm:grid sm:grid-cols-2">
            <div class="relative aspect-[4/5] overflow-hidden bg-[#E8F0E5]">
                <img id="highlighted-preview-image" src="" alt="Preview highlighted product" class="absolute inset-0 h-full w-full object-contain p-4">
                <span id="highlighted-preview-placeholder" class="absolute inset-0 flex items-center justify-center px-5 text-center text-xs text-[#6D8A65]">Upload foto untuk melihat preview highlighted product</span>
            </div>
            <div class="flex flex-col justify-center p-4">
                <p id="highlighted-preview-name" class="text-lg font-semibold leading-tight text-[#171717]">Nama Produk</p>
                <p id="highlighted-preview-price" class="mt-2 text-xl font-bold text-[#2D5A27]">Rp 0</p>
                <p class="mt-3 text-xs leading-relaxed text-[#737373]">Foto ditampilkan penuh agar kemasan portrait tidak terpotong.</p>
            </div>
        </div>
    </div>
</div>

<div id="product-image-editor" class="fixed inset-0 z-[60] hidden items-center justify-center bg-[#171717]/70 p-4" role="dialog" aria-modal="true" aria-labelledby="product-image-editor-title">
    <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-[#E8F0E5] px-5 py-4">
            <div>
                <h3 id="product-image-editor-title" class="font-semibold text-[#171717]">Sesuaikan Foto</h3>
                <p class="mt-1 text-xs text-[#737373]">Geser foto untuk mengatur tampilannya.</p>
            </div>
            <button type="button" id="product-image-editor-close" class="rounded-full p-2 text-[#737373] hover:bg-[#F5F9F3]" aria-label="Tutup"><i class="fas fa-times"></i></button>
        </div>
        <div id="product-image-editor-stage" class="mx-auto my-5 aspect-[4/5] w-64 touch-none overflow-hidden rounded-xl bg-[#F5F9F3]">
            <img id="product-image-editor-image" src="" alt="Penyesuaian foto" class="h-full w-full select-none object-cover" draggable="false">
        </div>
        <div class="grid grid-cols-2 gap-3 px-5 pb-5">
            <button type="button" data-fit-mode="cover" class="image-fit-option rounded-xl border border-[#2D5A27] bg-[#2D5A27] px-3 py-2 text-left text-xs text-white">
                <span class="block font-semibold">Isi Area</span><span class="mt-1 block opacity-80">Foto memenuhi area kartu.</span>
            </button>
            <button type="button" data-fit-mode="contain" class="image-fit-option rounded-xl border border-[#E8F0E5] bg-white px-3 py-2 text-left text-xs text-[#171717]">
                <span class="block font-semibold">Foto Penuh</span><span class="mt-1 block text-[#737373]">Seluruh produk terlihat.</span>
            </button>
        </div>
        <div class="flex justify-end gap-2 border-t border-[#E8F0E5] px-5 py-4">
            <button type="button" id="product-image-editor-cancel" class="rounded-xl px-4 py-2 text-sm text-[#737373] hover:bg-[#F5F9F3]">Batal</button>
            <button type="button" id="product-image-editor-apply" class="rounded-xl bg-[#2D5A27] px-4 py-2 text-sm font-semibold text-white hover:bg-[#1E3D1A]">Terapkan</button>
        </div>
    </div>
</div>

@push('styles')
<style>
    #product-image-manager .image-draft-card { min-height: 190px; }
    #product-image-manager .image-draft-card.is-primary { border-color: #2D5A27; box-shadow: 0 0 0 2px #E8F0E5; }
</style>
@endpush

<script>
window.ProductImageManager = (function () {
    const input = document.getElementById('product-images-input');
    const list = document.getElementById('product-image-list');
    const metaInput = document.getElementById('product-image-meta');
    const removeInputs = document.getElementById('remove-image-inputs');
    const dropzone = document.getElementById('product-upload-dropzone');
    const message = document.getElementById('product-image-message');
    const editor = document.getElementById('product-image-editor');
    const editorStage = document.getElementById('product-image-editor-stage');
    const editorImage = document.getElementById('product-image-editor-image');
    const editorClose = document.getElementById('product-image-editor-close');
    const editorCancel = document.getElementById('product-image-editor-cancel');
    const editorApply = document.getElementById('product-image-editor-apply');
    const drafts = @json($imageDrafts);
    let editorIndex = null;
    let editorDraft = null;
    let dragState = null;

    function showMessage(text, type = 'warning') {
        message.textContent = text;
        message.className = `mt-3 rounded-xl border px-3 py-2 text-xs ${type === 'error' ? 'border-red-200 bg-red-50 text-red-700' : 'border-amber-200 bg-amber-50 text-amber-700'}`;
    }

    function clearMessage() {
        message.textContent = '';
        message.className = 'mt-3 hidden rounded-xl border px-3 py-2 text-xs';
    }

    function syncFiles() {
        const transfer = new DataTransfer();
        drafts.forEach(draft => { if (draft.file) transfer.items.add(draft.file); });
        input.files = transfer.files;
    }

    function syncFormState() {
        const fileIndex = new Map();
        let nextFileIndex = 0;
        const meta = drafts.map(draft => {
            const item = {
                id: draft.id || null,
                fit_mode: draft.fit_mode || 'cover',
                focal_x: draft.focal_x ?? 50,
                focal_y: draft.focal_y ?? 50,
                alt_text: draft.alt_text || '',
            };
            if (draft.file) {
                item.file_index = nextFileIndex;
                nextFileIndex += 1;
            }
            return item;
        });
        metaInput.value = JSON.stringify(meta);
        removeInputs.innerHTML = drafts.removedIds.map(id => `<input type="hidden" name="remove_image_ids[]" value="${id}">`).join('');
        syncFiles();
    }

    drafts.removedIds = drafts.removedIds || [];

    function imageStyle(draft) {
        return `object-fit:${draft.fit_mode === 'contain' ? 'contain' : 'cover'};object-position:${draft.focal_x ?? 50}% ${draft.focal_y ?? 50}%;background:#F5F9F3;`;
    }

    function renderList() {
        list.innerHTML = '';
        drafts.forEach((draft, index) => {
            const card = document.createElement('div');
            card.className = `image-draft-card relative overflow-hidden rounded-2xl border bg-white ${index === 0 ? 'is-primary border-[#2D5A27]' : 'border-[#E8F0E5]'}`;
            const src = draft.previewUrl || draft.url;
            card.innerHTML = `
                <div class="relative aspect-[4/5] overflow-hidden bg-[#F5F9F3]">
                    <img src="${src || ''}" alt="Foto produk ${index + 1}" class="h-full w-full" style="${imageStyle(draft)}">
                    <span class="absolute left-2 top-2 rounded-full bg-white/95 px-2 py-1 text-[10px] font-bold text-[#2D5A27]">${index === 0 ? 'Foto Utama' : `Foto ${index + 1}`}</span>
                </div>
                <div class="flex items-center justify-between gap-1 p-2">
                    <button type="button" class="image-adjust rounded-lg px-2 py-1 text-[11px] font-semibold text-[#2D5A27] hover:bg-[#F5F9F3]" data-index="${index}">Sesuaikan Foto</button>
                    <div class="flex items-center gap-1">
                        <button type="button" class="image-up rounded-lg px-2 py-1 text-xs text-[#737373] hover:bg-[#F5F9F3]" data-index="${index}" aria-label="Naik">↑</button>
                        <button type="button" class="image-down rounded-lg px-2 py-1 text-xs text-[#737373] hover:bg-[#F5F9F3]" data-index="${index}" aria-label="Turun">↓</button>
                        <button type="button" class="image-remove rounded-lg px-2 py-1 text-xs text-red-600 hover:bg-red-50" data-index="${index}" aria-label="Hapus">×</button>
                    </div>
                </div>`;
            list.appendChild(card);
        });
        syncFormState();
        updateCatalogPreview();
    }

    function updateCatalogPreview() {
        const draft = drafts[0];
        const image = document.getElementById('catalog-preview-image');
        const placeholder = document.getElementById('catalog-preview-placeholder');
        const highlightedImage = document.getElementById('highlighted-preview-image');
        const highlightedPlaceholder = document.getElementById('highlighted-preview-placeholder');
        const name = document.getElementById('product-name');
        const price = document.querySelector('input[name="variation_price[]"]')?.value || document.querySelector('input[name="price"]')?.value;
        const category = document.querySelector('select[name="category_id"]');
        const categoryOption = category?.options[category.selectedIndex];
        const src = draft?.previewUrl || draft?.url || '';
        image.src = src;
        image.style.cssText = imageStyle(draft || {});
        image.classList.toggle('hidden', !src);
        placeholder.classList.toggle('hidden', !!src);
        document.getElementById('catalog-preview-name').textContent = name?.value || 'Nama Produk';
        document.getElementById('catalog-preview-price').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(Number(price || 0))}`;
        document.getElementById('catalog-preview-category').textContent = categoryOption?.textContent || '';

        highlightedImage.src = src;
        highlightedImage.classList.toggle('hidden', !src);
        highlightedPlaceholder.classList.toggle('hidden', !!src);
        document.getElementById('highlighted-preview-name').textContent = name?.value || 'Nama Produk';
        document.getElementById('highlighted-preview-price').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(Number(price || 0))}`;
    }

    function addFiles(fileList) {
        clearMessage();
        Array.from(fileList || []).forEach(file => {
            if (!/^image\/(jpeg|png|webp|avif)$/.test(file.type)) {
                showMessage('Format foto harus JPG, PNG, WebP, atau AVIF.');
                return;
            }
            if (file.size > 10 * 1024 * 1024) {
                showMessage('Foto terlalu besar. Ukuran maksimal adalah 10 MB.');
                return;
            }
            if (drafts.length >= 5) {
                showMessage('Maksimal 5 foto produk.');
                return;
            }
            const previewUrl = URL.createObjectURL(file);
            const draft = { id: null, file, previewUrl, fit_mode: 'cover', focal_x: 50, focal_y: 50, alt_text: '' };
            const probe = new Image();
            probe.onload = () => {
                draft.width = probe.naturalWidth;
                draft.height = probe.naturalHeight;
                if (draft.width < 800 || draft.height < 800) showMessage('Kualitas foto mungkin kurang tajam. Gunakan foto minimal 800 px agar hasil katalog tetap bagus.');
            };
            probe.src = previewUrl;
            drafts.push(draft);
        });
        renderList();
    }

    function openEditor(index) {
        editorIndex = index;
        editorDraft = { ...drafts[index] };
        editorImage.src = editorDraft.previewUrl || editorDraft.url;
        applyEditorStyle();
        editor.classList.remove('hidden');
        editor.classList.add('flex');
    }

    function closeEditor() {
        editor.classList.add('hidden');
        editor.classList.remove('flex');
        editorIndex = null;
        editorDraft = null;
    }

    function applyEditorStyle() {
        editorImage.style.cssText = imageStyle(editorDraft || {});
        document.querySelectorAll('.image-fit-option').forEach(button => {
            const active = button.dataset.fitMode === (editorDraft?.fit_mode || 'cover');
            button.className = `image-fit-option rounded-xl border px-3 py-2 text-left text-xs ${active ? 'border-[#2D5A27] bg-[#2D5A27] text-white' : 'border-[#E8F0E5] bg-white text-[#171717]'}`;
        });
    }

    list.addEventListener('click', event => {
        const button = event.target.closest('button');
        if (!button) return;
        const index = Number(button.dataset.index);
        if (button.classList.contains('image-adjust')) openEditor(index);
        if (button.classList.contains('image-remove')) {
            if (drafts[index].id) drafts.removedIds.push(drafts[index].id);
            if (drafts[index].previewUrl) URL.revokeObjectURL(drafts[index].previewUrl);
            drafts.splice(index, 1);
            renderList();
        }
        if (button.classList.contains('image-up') && index > 0) [drafts[index - 1], drafts[index]] = [drafts[index], drafts[index - 1]];
        if (button.classList.contains('image-down') && index < drafts.length - 1) [drafts[index + 1], drafts[index]] = [drafts[index], drafts[index + 1]];
        if (button.classList.contains('image-up') || button.classList.contains('image-down')) renderList();
    });

    input.addEventListener('change', () => { addFiles(input.files); });
    ['dragenter', 'dragover'].forEach(eventName => dropzone.addEventListener(eventName, event => { event.preventDefault(); dropzone.classList.add('border-[#2D5A27]', 'bg-[#F5F9F3]'); }));
    ['dragleave', 'drop'].forEach(eventName => dropzone.addEventListener(eventName, event => { event.preventDefault(); dropzone.classList.remove('border-[#2D5A27]', 'bg-[#F5F9F3]'); }));
    dropzone.addEventListener('drop', event => addFiles(event.dataTransfer.files));

    document.querySelector('form#product-form')?.addEventListener('input', updateCatalogPreview);
    document.querySelectorAll('.image-fit-option').forEach(button => button.addEventListener('click', () => { editorDraft.fit_mode = button.dataset.fitMode; applyEditorStyle(); }));
    editorStage.addEventListener('pointerdown', event => { if (!editorDraft || editorDraft.fit_mode !== 'cover') return; editorStage.setPointerCapture(event.pointerId); dragState = { x: event.clientX, y: event.clientY, focalX: editorDraft.focal_x, focalY: editorDraft.focal_y }; });
    editorStage.addEventListener('pointermove', event => { if (!dragState) return; editorDraft.focal_x = Math.max(0, Math.min(100, dragState.focalX - (event.clientX - dragState.x) / editorStage.clientWidth * 100)); editorDraft.focal_y = Math.max(0, Math.min(100, dragState.focalY - (event.clientY - dragState.y) / editorStage.clientHeight * 100)); applyEditorStyle(); });
    ['pointerup', 'pointercancel'].forEach(name => editorStage.addEventListener(name, () => { dragState = null; }));
    [editorClose, editorCancel].forEach(button => button.addEventListener('click', closeEditor));
    editorApply.addEventListener('click', () => { drafts[editorIndex] = { ...drafts[editorIndex], ...editorDraft }; renderList(); closeEditor(); });

    renderList();
    return { selectFiles: addFiles };
})();
</script>
