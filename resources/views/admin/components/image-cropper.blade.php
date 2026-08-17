{{-- Interactive Image Cropper Modal for Admin Panel --}}
<div id="adminCropperModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="cropper-modal-title" role="dialog" aria-modal="true">
    {{-- Overlay --}}
    <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-xs transition-opacity"></div>

    {{-- Modal Dialog --}}
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-6">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-slate-200 flex flex-col max-h-[90vh]">
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/70">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-base">
                        <i class="fas fa-crop-simple"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 leading-tight" id="cropper-modal-title">Sesuaikan & Potong Gambar</h3>
                        <p class="text-xs text-slate-500 mt-0.5" id="cropper-queue-info">Atur posisi, zoom, dan sudut gambar sesuai kebutuhan</p>
                    </div>
                </div>
                <button type="button" onclick="window.AdminCropper.close()" class="text-slate-400 hover:text-slate-600 rounded-lg p-1.5 hover:bg-slate-100 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            {{-- Main Canvas Body --}}
            <div class="p-6 overflow-y-auto flex-1 flex flex-col items-center justify-center bg-slate-950/5 min-h-[360px]">
                <div class="w-full max-h-[460px] flex items-center justify-center overflow-hidden rounded-xl bg-slate-900">
                    <img id="adminCropperImage" src="" alt="Crop Preview" class="max-w-full max-h-[440px] block">
                </div>
            </div>

            {{-- Controls & Locked Aspect Ratio --}}
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                {{-- The upload context supplies exactly one locked ratio. --}}
                <div class="flex items-center gap-1.5 flex-wrap" id="cropperRatioButtons">
                    <span class="text-xs font-semibold text-slate-500 mr-1"><i class="fas fa-shapes mr-1"></i>Rasio:</span>
                    <span id="cropperLockedRatio" class="px-2.5 py-1 text-xs font-bold rounded-lg border border-blue-600 bg-blue-600 text-white" aria-live="polite"></span>
                </div>

                {{-- Toolbar Actions (Rotate, Flip, Zoom, Reset) --}}
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button type="button" onclick="window.AdminCropper.rotate(-90)" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 flex items-center justify-center text-xs shadow-xs" title="Putar Kiri 90°">
                        <i class="fas fa-rotate-left"></i>
                    </button>
                    <button type="button" onclick="window.AdminCropper.rotate(90)" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 flex items-center justify-center text-xs shadow-xs" title="Putar Kanan 90°">
                        <i class="fas fa-rotate-right"></i>
                    </button>
                    <button type="button" onclick="window.AdminCropper.flipX()" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 flex items-center justify-center text-xs shadow-xs" title="Balik Horizontal">
                        <i class="fas fa-arrows-left-right"></i>
                    </button>
                    <button type="button" onclick="window.AdminCropper.zoom(0.1)" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 flex items-center justify-center text-xs shadow-xs" title="Perbesar (+)">
                        <i class="fas fa-magnifying-glass-plus"></i>
                    </button>
                    <button type="button" onclick="window.AdminCropper.zoom(-0.1)" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 flex items-center justify-center text-xs shadow-xs" title="Perkecil (-)">
                        <i class="fas fa-magnifying-glass-minus"></i>
                    </button>
                    <button type="button" onclick="window.AdminCropper.reset()" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 flex items-center justify-center text-xs shadow-xs" title="Reset Posisi">
                        <i class="fas fa-arrow-rotate-right"></i>
                    </button>
                </div>
            </div>

            {{-- Footer Buttons --}}
            <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between bg-white">
                <span class="text-xs text-slate-500">Rasio dikunci sesuai kebutuhan gambar</span>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="window.AdminCropper.close()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="button" onclick="window.AdminCropper.apply()" class="px-5 py-2.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-xs transition-colors flex items-center gap-1.5">
                        <i class="fas fa-check"></i>
                        <span id="cropperApplyBtnText">Terapkan & Potong</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.AdminCropper = (function() {
    let cropperInstance = null;
    let targetInput = null;
    let previewCallback = null;
    let defaultRatio = 1;
    let lockedRatio = 1;
    let ratioLabel = '1:1';
    let fileQueue = [];
    let currentFileIndex = 0;
    let processedFiles = [];
    let scaleX = 1;
    let scaleY = 1;

    const modal = document.getElementById('adminCropperModal');
    const imageEl = document.getElementById('adminCropperImage');
    const queueInfo = document.getElementById('cropper-queue-info');
    const applyBtnText = document.getElementById('cropperApplyBtnText');

    function initForInput(inputElement, options = {}) {
        if (!inputElement || !inputElement.files || inputElement.files.length === 0) return;

        targetInput = inputElement;
        defaultRatio = typeof options.aspectRatio !== 'undefined' ? options.aspectRatio : 1;
        lockedRatio = defaultRatio;
        ratioLabel = options.ratioLabel || `${defaultRatio}:1`;
        previewCallback = options.onComplete || null;

        fileQueue = Array.from(inputElement.files);
        currentFileIndex = 0;
        processedFiles = [];

        processNextInQueue();
    }

    function processNextInQueue() {
        if (currentFileIndex >= fileQueue.length) {
            finishCropping();
            return;
        }

        const currentFile = fileQueue[currentFileIndex];
        if (!currentFile.type.startsWith('image/')) {
            // Not an image, preserve as is
            processedFiles.push(currentFile);
            currentFileIndex++;
            processNextInQueue();
            return;
        }

        if (fileQueue.length > 1) {
            queueInfo.textContent = `Memproses foto ${currentFileIndex + 1} dari ${fileQueue.length}: ${currentFile.name}`;
            applyBtnText.textContent = currentFileIndex + 1 === fileQueue.length ? 'Selesai & Terapkan' : 'Terapkan & Lanjut';
        } else {
            queueInfo.textContent = 'Atur posisi, zoom, dan sudut gambar sesuai kebutuhan';
            applyBtnText.textContent = 'Terapkan & Potong';
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            openModalWithImage(e.target.result);
        };
        reader.readAsDataURL(currentFile);
    }

    function openModalWithImage(dataUrl) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }

        imageEl.src = dataUrl;
        scaleX = 1;
        scaleY = 1;

        document.getElementById('cropperLockedRatio').textContent = ratioLabel;

        // Wait for image render
        setTimeout(() => {
            cropperInstance = new Cropper(imageEl, {
                aspectRatio: defaultRatio,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        }, 100);
    }

    function setAspectRatio(ratio, btnElement) {
        // Kept for backwards compatibility, but uploads always use their locked profile.
        defaultRatio = lockedRatio;
        if (cropperInstance) cropperInstance.setAspectRatio(lockedRatio);
    }

    function rotate(degrees) {
        if (cropperInstance) {
            cropperInstance.rotate(degrees);
        }
    }

    function flipX() {
        if (cropperInstance) {
            scaleX = -scaleX;
            cropperInstance.scaleX(scaleX);
        }
    }

    function zoom(ratio) {
        if (cropperInstance) {
            cropperInstance.zoom(ratio);
        }
    }

    function reset() {
        if (cropperInstance) {
            cropperInstance.reset();
            scaleX = 1;
            scaleY = 1;
        }
    }

    function skipCurrent() {
        if (currentFileIndex < fileQueue.length) {
            processedFiles.push(fileQueue[currentFileIndex]);
        }
        currentFileIndex++;
        processNextInQueue();
    }

    function apply() {
        if (!cropperInstance) return;

        const currentFile = fileQueue[currentFileIndex];
        const canvas = cropperInstance.getCroppedCanvas({
            maxWidth: 1600,
            maxHeight: 1600,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        if (!canvas) {
            skipCurrent();
            return;
        }

        canvas.toBlob(function(blob) {
            const ext = currentFile.name.split('.').pop();
            const fileName = currentFile.name.replace(/\.[^/.]+$/, "") + "_cropped.jpg";
            const croppedFile = new File([blob], fileName, { type: 'image/jpeg', lastModified: Date.now() });

            processedFiles.push(croppedFile);
            currentFileIndex++;
            processNextInQueue();
        }, 'image/jpeg', 0.92);
    }

    function finishCropping() {
        close();

        if (targetInput && processedFiles.length > 0) {
            const dataTransfer = new DataTransfer();
            processedFiles.forEach(file => dataTransfer.items.add(file));
            targetInput.files = dataTransfer.files;

            // Trigger onchange on target input for preview handlers
            if (typeof previewCallback === 'function') {
                previewCallback(targetInput);
            } else {
                targetInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    }

    function close() {
        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    return {
        initForInput,
        setAspectRatio,
        rotate,
        flipX,
        zoom,
        reset,
        skipCurrent,
        apply,
        close
    };
})();
</script>
