<x-admin::layouts>
    <x-slot:title>
        Homepage Manager
    </x-slot>

    <div class="flex items-center justify-between gap-4 mb-5">
        <div class="grid gap-1">
            <h1 class="text-xl font-bold text-gray-800 dark:text-white">Homepage Manager</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Atur produk & kategori yang tampil di section homepage. Section kosong otomatis pakai mode <span class="font-semibold">otomatis</span>.
            </p>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        @foreach ($sections as $sectionKey => $section)
            @php
                $meta       = $section['meta'];
                $highlights = $section['highlights'];
                $count      = $section['count'];
                $isCategory = $meta['type'] === \Beres\Highlight\Models\HomepageHighlight::TYPE_CATEGORY;
            @endphp

            <div
                class="bg-white rounded-lg border border-gray-200 dark:bg-gray-900 dark:border-gray-800 overflow-hidden"
                data-section="{{ $sectionKey }}"
                data-entity-type="{{ $meta['type'] }}"
            >
                {{-- Header --}}
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-800">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800 dark:text-white">{{ $meta['label'] }}</h2>
                        <p class="text-xs text-gray-400">{{ $meta['description'] }}</p>
                    </div>
                    <span class="shrink-0 text-xs font-medium px-2 py-1 rounded-full {{ $count > 0 ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-800' }}">
                        {{ $count }}{{ $meta['limit'] !== null ? '/'.$meta['limit'] : '' }}
                        @if ($count === 0)
                            · Auto
                        @endif
                    </span>
                </div>

                {{-- Pinned items list --}}
                <div
                    class="pinned-list divide-y divide-gray-100 dark:divide-gray-800 min-h-[40px]"
                    data-section="{{ $sectionKey }}"
                >
                    @forelse ($highlights as $hl)
                        @php
                            $entity = $hl->entity;
                            $name   = $entity?->name ?? '(entitas tidak ditemukan)';
                            $sub    = $entity?->sku ?? $entity?->slug ?? '';
                        @endphp
                        <div
                            class="flex items-center gap-2 px-4 py-2 group"
                            data-highlight-id="{{ $hl->id }}"
                            data-entity-id="{{ $hl->entity_id }}"
                        >
                            <span class="flex flex-col gap-0.5">
                                <button
                                    type="button"
                                    class="move-up text-gray-300 hover:text-green-600 text-xs leading-none"
                                    title="Naik"
                                >▲</button>
                                <button
                                    type="button"
                                    class="move-down text-gray-300 hover:text-green-600 text-xs leading-none"
                                    title="Turun"
                                >▼</button>
                            </span>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $name }}</p>
                                @if ($sub)
                                    <p class="text-xs text-gray-400 truncate">{{ $sub }}</p>
                                @endif
                            </div>

                            <button
                                type="button"
                                class="unpin-btn shrink-0 opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 text-sm transition-opacity"
                                title="Lepas"
                            >✕</button>
                        </div>
                    @empty
                        <div class="px-4 py-3 text-xs text-gray-400 italic">
                            Belum ada item dipin — section ini berjalan otomatis.
                        </div>
                    @endforelse
                </div>

                {{-- Search & add --}}
                @if ($meta['limit'] === null || $count < $meta['limit'])
                        <div class="border-t border-gray-200 dark:border-gray-800 px-4 py-3 relative" data-search-container>
                            <input
                                type="text"
                                class="search-input w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-800 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:border-green-500 focus:ring-green-500"
                                placeholder="Cari {{ $isCategory ? 'kategori' : 'produk' }} untuk dipin..."
                                autocomplete="off"
                            >
                            <div class="search-results absolute left-4 right-4 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-y-auto hidden z-10"></div>
                        </div>
                @else
                    <div class="border-t border-gray-200 dark:border-gray-800 px-4 py-2 text-xs text-center text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20">
                        Section penuh — hapus item untuk menambah baru.
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- ====================================================================
         GT-Strings Container
         Visually hidden but present in DOM so Google Translate scans &
         translates these on its pass. JS reads them back via t(key) so
         dynamically inserted text (flash messages, dynamic rows) is also
         translated in the active language.
    ==================================================================== --}}
    <div class="sr-only" id="gt-strings" aria-hidden="true">
        <span data-key="pin_success">Item berhasil dipin.</span>
        <span data-key="unpin_success">Item berhasil dilepas.</span>
        <span data-key="reorder_success">Urutan berhasil disimpan.</span>
        <span data-key="add_failed">Gagal menambahkan item.</span>
        <span data-key="already_pinned">Item ini sudah dipin ke section tersebut.</span>
        <span data-key="section_full">Section sudah penuh. Lepas salah satu untuk menambah.</span>
        <span data-key="type_mismatch">Tipe entitas tidak cocok untuk section ini.</span>
        <span data-key="no_results">Tidak ada hasil.</span>
        <span data-key="empty_state">Belum ada item dipin — section ini berjalan otomatis.</span>
        <span data-key="section_full_notice">Section penuh — hapus item untuk menambah baru.</span>
        <span data-key="pin_button">+ Pin</span>
        <span data-key="move_up">Naik</span>
        <span data-key="move_down">Turun</span>
        <span data-key="unpin">Lepas</span>
        <span data-key="auto">Auto</span>
        <span data-key="search_product">Cari produk untuk dipin...</span>
        <span data-key="search_category">Cari kategori untuk dipin...</span>
        <span data-key="entity_not_found">entitas tidak ditemukan</span>
    </div>

    @push('scripts')
        <script>
            (function () {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

                // ── GT-translated strings ──────────────────────────────
                // Reads back the translated text nodes that GT wrote on
                // its pass.  Falls back to the key itself if not found.
                function t(key) {
                    var el = document.querySelector('#gt-strings [data-key="' + key + '"]');
                    return el ? el.textContent.trim() : key;
                }

                function post(url, data) {
                    return fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(data),
                    }).then(r => r.json());
                }

                function del(url) {
                    return fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                    }).then(r => r.json());
                }

                function flash(message, type) {
                    window.dispatchEvent(new CustomEvent('add-flash', { detail: { type, message } }));
                }

                document.querySelectorAll('[data-section]').forEach(function (card) {
                    if (! card.dataset.entityType) return;

                    const section     = card.dataset.section;
                    const entityType  = card.dataset.entityType;
                    const pinnedList  = card.querySelector('.pinned-list');
                    const searchInput = card.querySelector('.search-input');
                    const resultsBox  = card.querySelector('.search-results');

                    if (! pinnedList) return;

                    const SECTION_URL = '{{ route("admin.homepage_manager.store") }}';
                    const REORDER_URL = '{{ route("admin.homepage_manager.reorder") }}';
                    const SEARCH_URL  = '{{ route("admin.homepage_manager.search") }}';

                    // ── Render a pinned item row ──
                    function pinnedRowHtml(item) {
                        const sub = item.sku || item.slug || '';
                        return '<div class="flex items-center gap-2 px-4 py-2 group" data-highlight-id="' + item.highlight_id + '" data-entity-id="' + item.entity_id + '">'
                            + '<span class="flex flex-col gap-0.5">'
                            +   '<button type="button" class="move-up text-gray-300 hover:text-green-600 text-xs leading-none" title="' + t('move_up') + '">▲</button>'
                            +   '<button type="button" class="move-down text-gray-300 hover:text-green-600 text-xs leading-none" title="' + t('move_down') + '">▼</button>'
                            + '</span>'
                            + '<div class="flex-1 min-w-0">'
                            +   '<p class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">' + item.name + '</p>'
                            +   (sub ? '<p class="text-xs text-gray-400 truncate">' + sub + '</p>' : '')
                            + '</div>'
                            + '<button type="button" class="unpin-btn shrink-0 opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 text-sm transition-opacity" title="' + t('unpin') + '">✕</button>'
                            + '</div>';
                    }

                    // ── Pin an entity ──
                    function pinEntity(entityId) {
                        post(SECTION_URL, {
                            section: section,
                            entity_type: entityType,
                            entity_id: entityId,
                        }).then(function (res) {
                            if (res.success) {
                                pinnedList.insertAdjacentHTML('beforeend', pinnedRowHtml(res.highlight));
                                removeEmptyMessage();
                                updateCount(1);
                                searchInput.value = '';
                                resultsBox.classList.add('hidden');
                                checkFull();
                                flash(t(res.message_key || 'pin_success'), 'success');
                            } else {
                                flash(t(res.message_key || 'add_failed'), 'error');
                            }
                        });
                    }

                    // ── Search (debounced) ──
                    let searchTimer = null;
                    if (searchInput) {
                        searchInput.addEventListener('input', function () {
                            clearTimeout(searchTimer);
                            const q = searchInput.value.trim();

                            if (q.length < 1) {
                                resultsBox.classList.add('hidden');
                                return;
                            }

                            searchTimer = setTimeout(function () {
                                const params = new URLSearchParams({ q, entity_type: entityType, section });
                                fetch(SEARCH_URL + '?' + params.toString(), {
                                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                                })
                                    .then(r => r.json())
                                    .then(function (res) {
                                        if (! res.success || ! res.data.length) {
                                            resultsBox.innerHTML = '<p class="px-3 py-2 text-xs text-gray-400">' + t('no_results') + '</p>';
                                            resultsBox.classList.remove('hidden');
                                            return;
                                        }

                                        resultsBox.innerHTML = res.data.map(function (item) {
                                            const sub = item.sku || item.slug || '';
                                            return '<button type="button" class="add-btn w-full flex items-center justify-between gap-2 px-3 py-2 text-left hover:bg-green-50 dark:hover:bg-green-900/30 border-b border-gray-100 dark:border-gray-700 last:border-0"'
                                                + ' data-id="' + item.id + '" data-name="' + item.name + '" data-sku="' + (item.sku || '') + '" data-slug="' + (item.slug || '') + '">'
                                                + '<div class="min-w-0">'
                                                +   '<p class="text-sm text-gray-700 dark:text-gray-300 truncate">' + item.name + '</p>'
                                                +   (sub ? '<p class="text-xs text-gray-400 truncate">' + sub + '</p>' : '')
                                                + '</div>'
                                                + '<span class="shrink-0 text-green-600 text-xs font-semibold">' + t('pin_button') + '</span>'
                                                + '</button>';
                                        }).join('');
                                        resultsBox.classList.remove('hidden');
                                    });
                            }, 300);
                        });

                        searchInput.addEventListener('blur', function () {
                            setTimeout(function () { resultsBox.classList.add('hidden'); }, 200);
                        });
                    }

                    resultsBox?.addEventListener('click', function (e) {
                        const btn = e.target.closest('.add-btn');
                        if (! btn) return;
                        pinEntity(parseInt(btn.dataset.id));
                    });

                    // ── Unpin ──
                    pinnedList.addEventListener('click', function (e) {
                        const btn = e.target.closest('.unpin-btn');
                        if (! btn) return;

                        const row = btn.closest('[data-highlight-id]');
                        const id  = row.dataset.highlightId;

                        del('{{ route("admin.homepage_manager.destroy", ':id') }}'.replace(':id', id))
                            .then(function (res) {
                                if (res.success) {
                                    row.remove();
                                    updateCount(-1);
                                    addEmptyMessageIfNeeded();
                                    checkFull();
                                    flash(t(res.message_key || 'unpin_success'), 'success');
                                }
                            });
                    });

                    // ── Reorder (up / down) ──
                    pinnedList.addEventListener('click', function (e) {
                        const upBtn   = e.target.closest('.move-up');
                        const downBtn = e.target.closest('.move-down');
                        if (! upBtn && ! downBtn) return;

                        const row  = (upBtn || downBtn).closest('[data-highlight-id]');
                        const rows = [...pinnedList.querySelectorAll('[data-highlight-id]')];
                        const idx  = rows.indexOf(row);

                        if (upBtn && idx > 0) {
                            pinnedList.insertBefore(row, rows[idx - 1]);
                        } else if (downBtn && idx < rows.length - 1) {
                            pinnedList.insertBefore(row, rows[idx + 1].nextSibling);
                        } else {
                            return;
                        }

                        const newOrder = [...pinnedList.querySelectorAll('[data-highlight-id]')].map(r => parseInt(r.dataset.highlightId));
                        post(REORDER_URL, { section, ids: newOrder }).then(function () {
                            flash(t('reorder_success'), 'success');
                        });
                    });

                    // ── Helpers ──
                    function removeEmptyMessage() {
                        const empty = pinnedList.querySelector('.italic');
                        if (empty) empty.remove();
                    }

                    function addEmptyMessageIfNeeded() {
                        if (! pinnedList.querySelector('[data-highlight-id]')) {
                            pinnedList.innerHTML = '<div class="px-4 py-3 text-xs text-gray-400 italic">' + t('empty_state') + '</div>';
                        }
                    }

                    function updateCount(delta) {
                        const badge = card.querySelector('.rounded-full');
                        if (! badge) return;
                        const text = badge.textContent.trim();
                        const m = text.match(/(\d+)\/(\d+)/);
                        if (! m) return;
                        const cur = Math.max(0, parseInt(m[1]) + delta);
                        const max = m[2];
                        const isAuto = cur === 0;
                        badge.textContent = cur + '/' + max + (isAuto ? ' · ' + t('auto') : '');
                        badge.className = badge.className.replace(/bg-\w+-100 text-\w+-700 dark:bg-\w+-900 dark:text-\w+-300|bg-gray-100 text-gray-500 dark:bg-gray-800/, isAuto ? 'bg-gray-100 text-gray-500 dark:bg-gray-800' : 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300');
                    }

                    function checkFull() {
                        const count = pinnedList.querySelectorAll('[data-highlight-id]').length;
                        const badge = card.querySelector('.rounded-full');
                        const m = badge ? badge.textContent.match(/\/(\d+)/) : null;
                        const limit = m ? parseInt(m[1]) : 99;

                        if (count >= limit && card.querySelector('[data-search-container]')) {
                            location.reload();
                        } else if (count < limit && ! card.querySelector('[data-search-container]')) {
                            location.reload();
                        }
                    }
                });
            })();
        </script>
    @endpush
</x-admin::layouts>
