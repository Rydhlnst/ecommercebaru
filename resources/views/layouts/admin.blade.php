<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — Ankish Mart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --bg-body: #f3f4f6;
            --bg-white: #ffffff;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --sidebar-active: #2563eb;
            --sidebar-text: #cbd5e1;
            --sidebar-text-active: #ffffff;
        }
        * { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        body { background: var(--bg-body); }
        .sidebar { width: 260px; min-height: 100vh; background: var(--sidebar-bg); transition: transform 0.3s ease; }
        .sidebar.collapsed { transform: translateX(-260px); }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 40; }
        .sidebar-overlay.active { display: block; }
        .sidebar-link { display: flex; align-items: center; padding: 0.625rem 1rem; color: var(--sidebar-text); border-radius: 0.5rem; margin: 0.125rem 0.5rem; transition: all 0.15s; text-decoration: none; font-size: 0.875rem; }
        .sidebar-link:hover { background: var(--sidebar-hover); color: var(--sidebar-text-active); }
        .sidebar-link.active { background: var(--sidebar-active); color: var(--sidebar-text-active); font-weight: 600; }
        .sidebar-link i { width: 1.5rem; text-align: center; margin-right: 0.75rem; font-size: 0.875rem; }
        .sidebar-heading { padding: 0.5rem 1rem; color: #94a3b8; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin-top: 0.5rem; }
        .dropdown-toggle { cursor: pointer; }
        .dropdown-menu { display: none; padding-left: 1rem; }
        .dropdown-menu.show { display: block; }
        .dropdown-arrow { transition: transform 0.2s; margin-left: auto; font-size: 0.75rem; }
        .dropdown-arrow.rotated { transform: rotate(90deg); }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th { background: #f8fafc; padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); border-bottom: 2px solid var(--border-color); }
        .admin-table td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); font-size: 0.875rem; }
        .admin-table tr:hover td { background: #f8fafc; }
        .admin-panel-card { background: var(--bg-white); border-radius: 0.75rem; border: 1px solid var(--border-color); padding: 1.5rem; }
        .admin-badge { display: inline-flex; align-items: center; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
        .admin-badge-pending { background: #fef3c7; color: #92400e; }
        .admin-badge-processing { background: #dbeafe; color: #1e40af; }
        .admin-badge-completed { background: #d1fae5; color: #065f46; }
        .admin-badge-cancelled { background: #fee2e2; color: #991b1b; }
        .admin-badge-paid { background: #d1fae5; color: #065f46; }
        .admin-badge-new { background: #dbeafe; color: #1e40af; }
        .admin-badge-sale { background: #fef3c7; color: #92400e; }
        .admin-badge-habis_terjual { background: #fee2e2; color: #991b1b; }
        .admin-badge-active { background: #d1fae5; color: #065f46; }
        .admin-badge-inactive { background: #f3f4f6; color: #6b7280; }
        .btn-primary { background: var(--primary-color); color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; transition: background 0.15s; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-danger { background: #dc2626; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; transition: background 0.15s; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-sm { padding: 0.375rem 0.75rem; font-size: 0.8125rem; }
        .form-input { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 0.875rem; transition: border-color 0.15s; }
        .form-input:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .form-label { display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem; }
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
        .page-header h1 { font-size: 1.5rem; font-weight: 700; color: #111827; }
        .stats-card { background: var(--bg-white); border-radius: 0.75rem; border: 1px solid var(--border-color); padding: 1.25rem; }
        .stats-card .stats-value { font-size: 1.75rem; font-weight: 700; color: #111827; }
        .stats-card .stats-label { font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.25rem; }
        .star-rating { color: #f59e0b; }
        @media (max-width: 991px) {
            .sidebar { position: fixed; z-index: 50; transform: translateX(-260px); }
            .sidebar.mobile-open { transform: translateX(0); }
        }
        @media (max-width: 576px) {
            .page-header { flex-direction: column; gap: 0.75rem; align-items: flex-start; }
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen">
    <div id="sidebar-overlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <aside id="sidebar" class="sidebar fixed top-0 left-0 z-50 overflow-y-auto">
        <div class="p-4 border-b border-slate-700">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 text-white font-bold text-lg">
                <i class="fas fa-store text-blue-400"></i>
                <span>Ankish Mart</span>
            </a>
        </div>
        <nav class="py-2">
            <div class="sidebar-heading">Menu Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>

            <div class="sidebar-heading">Manajemen</div>
            <div class="dropdown-toggle sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                <i class="fas fa-folder"></i> Kategori
                <i class="fas fa-chevron-right dropdown-arrow {{ request()->routeIs('admin.categories.*') ? 'rotated' : '' }}"></i>
            </div>
            <div class="dropdown-menu {{ request()->routeIs('admin.categories.*') ? 'show' : '' }}">
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link">Daftar Kategori</a>
                <a href="{{ route('admin.categories.create') }}" class="sidebar-link">Tambah Kategori</a>
            </div>

            <div class="dropdown-toggle sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                <i class="fas fa-box"></i> Produk
                <i class="fas fa-chevron-right dropdown-arrow {{ request()->routeIs('admin.products.*') ? 'rotated' : '' }}"></i>
            </div>
            <div class="dropdown-menu {{ request()->routeIs('admin.products.*') ? 'show' : '' }}">
                <a href="{{ route('admin.products.index') }}" class="sidebar-link">Daftar Produk</a>
                <a href="{{ route('admin.products.create') }}" class="sidebar-link">Tambah Produk</a>
            </div>

            <a href="{{ route('admin.showcase.index') }}" class="sidebar-link {{ request()->routeIs('admin.showcase.*') ? 'active' : '' }}">
                <i class="fas fa-star"></i> Showcase Produk
            </a>

            <div class="dropdown-toggle sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                <i class="fas fa-shopping-cart"></i> Pesanan
                <i class="fas fa-chevron-right dropdown-arrow {{ request()->routeIs('admin.orders.*') ? 'rotated' : '' }}"></i>
            </div>
            <div class="dropdown-menu {{ request()->routeIs('admin.orders.*') ? 'show' : '' }}">
                <a href="{{ route('admin.orders.index') }}" class="sidebar-link">Daftar Pesanan</a>
            </div>

            <div class="sidebar-heading">Konten</div>
            <div class="dropdown-toggle sidebar-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                <i class="fas fa-blog"></i> Blog
                <i class="fas fa-chevron-right dropdown-arrow {{ request()->routeIs('admin.blog.*') ? 'rotated' : '' }}"></i>
            </div>
            <div class="dropdown-menu {{ request()->routeIs('admin.blog.*') ? 'show' : '' }}">
                <a href="{{ route('admin.blog.index') }}" class="sidebar-link">Daftar Postingan</a>
                <a href="{{ route('admin.blog.create') }}" class="sidebar-link">Tambah Postingan</a>
            </div>

            <a href="{{ route('admin.reviews.index') }}" class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                <i class="fas fa-comments"></i> Komentar
            </a>

            <a href="{{ route('admin.faqs.index') }}" class="sidebar-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
                <i class="fas fa-question-circle"></i> FAQ
            </a>

            <div class="sidebar-heading">Pengaturan</div>
            <a href="{{ route('admin.settings.policy') }}" class="sidebar-link {{ request()->routeIs('admin.settings.policy') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> Privacy & Policy
            </a>
            <a href="{{ route('admin.settings.store') }}" class="sidebar-link {{ request()->routeIs('admin.settings.store') ? 'active' : '' }}">
                <i class="fas fa-cog"></i> Pengaturan Toko
            </a>

            <div class="sidebar-heading mt-4"></div>
            <form action="{{ route('admin.logout') }}" method="POST" id="logout-form">
                @csrf
                <button type="button" onclick="confirmLogout()" class="sidebar-link w-full text-left">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </nav>
    </aside>

    <main class="ml-0 lg:ml-[260px] min-h-screen transition-all duration-300">
        <header class="bg-white border-b border-gray-200 px-4 py-3 sticky top-0 z-30">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="lg:hidden text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h2 class="text-sm font-semibold text-gray-700">@yield('page-title', 'Dashboard')</h2>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative notranslate">
                        <button onclick="toggleLanguage()" class="flex items-center gap-1.5 px-3 py-1.5 text-sm border border-gray-200 rounded-lg hover:bg-gray-50" id="lang-toggle" title="Language">
                            <svg id="current-lang-flag" class="w-5 h-3.5 rounded-sm" viewBox="0 0 640 480"><path fill="#e70011" d="M0 0h640v240H0z"/><path fill="#fff" d="M0 240h640v240H0z"/></svg>
                            <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                        </button>
                        <div id="lang-dropdown" class="hidden absolute right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg py-1 z-50 min-w-[160px]">
                            <button type="button" data-gt-lang="en" onclick="setGoogleTranslateLang('en')" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 rounded flex items-center gap-2">
                                <svg class="w-5 h-3.5 rounded-sm" viewBox="0 0 640 480"><path fill="#012169" d="M0 0h640v480H0z"/><path fill="#FFF" d="m75 0 244 181L562 0h78v62L400 241l240 178v61h-80L320 301 80 480H0v-60l239-178L0 64V0z"/><path fill="#C8102E" d="m424 0 244 181 32-1h78v62L457 241l217 158v61h-80L377 301 240 480h-20v-60l239-178L0 64V0z"/><path fill="#FFF" d="M241 0v480h160V0zM0 160v160h640V160z"/><path fill="#C8102E" d="M0 193v96h640v-96zM273 0v480h96V0z"/></svg>
                                English
                            </button>
                            <button type="button" data-gt-lang="id" onclick="setGoogleTranslateLang('id')" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 rounded flex items-center gap-2">
                                <svg class="w-5 h-3.5 rounded-sm" viewBox="0 0 640 480"><path fill="#e70011" d="M0 0h640v240H0z"/><path fill="#fff" d="M0 240h640v240H0z"/></svg>
                                Bahasa Indonesia
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            {{ substr(auth('web')->user()->name ?? 'A', 0, 1) }}
                        </div>
                        <span class="text-sm font-medium text-gray-700 hidden sm:block">{{ auth('web')->user()->name ?? 'Admin' }}</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="p-4 sm:p-6">
            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', timer: 3000, showConfirmButton: false });
                    });
                </script>
            @endif
            @if(session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session('error') }}', timer: 3000, showConfirmButton: false });
                    });
                </script>
            @endif

            @yield('admin_content')
        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        }

        function toggleDropdown(el) {
            const menu = el.nextElementSibling;
            const arrow = el.querySelector('.dropdown-arrow');
            menu.classList.toggle('show');
            if (arrow) arrow.classList.toggle('rotated');
        }

        function confirmLogout() {
            Swal.fire({
                title: 'Logout?',
                text: 'Anda akan keluar dari panel admin.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        function toggleLanguage() {
            document.getElementById('lang-dropdown').classList.toggle('hidden');
        }

        // Same approach as the storefront front page: set the googtrans=/auto/<lang>
        // cookie (1-year, on both the plain path and the wildcard domain) then reload.
        function setGoogleTranslateLang(lang) {
            var expires = new Date();
            expires.setFullYear(expires.getFullYear() + 1);
            var val = '/auto/' + lang;
            document.cookie = 'googtrans=' + val + '; expires=' + expires.toUTCString() + '; path=/';
            document.cookie = 'googtrans=' + val + '; expires=' + expires.toUTCString() + '; path=/; domain=.' + location.hostname;
            location.reload();
        }

        // Reflect the active language on the toggle flag (matches the storefront).
        (function() {
            var match = document.cookie.match(/googtrans=\/auto\/([a-z]+)/);
            var currentLang = match ? match[1] : 'id';
            var flags = {
                en: '<svg class="w-5 h-3.5 rounded-sm" viewBox="0 0 640 480"><path fill="#012169" d="M0 0h640v480H0z"/><path fill="#FFF" d="m75 0 244 181L562 0h78v62L400 241l240 178v61h-80L320 301 80 480H0v-60l239-178L0 64V0z"/><path fill="#C8102E" d="m424 0 244 181 32-1h78v62L457 241l217 158v61h-80L377 301 240 480h-20v-60l239-178L0 64V0z"/><path fill="#FFF" d="M241 0v480h160V0zM0 160v160h640V160z"/><path fill="#C8102E" d="M0 193v96h640v-96zM273 0v480h96V0z"/></svg>',
                id: '<svg class="w-5 h-3.5 rounded-sm" viewBox="0 0 640 480"><path fill="#e70011" d="M0 0h640v240H0z"/><path fill="#fff" d="M0 240h640v240H0z"/></svg>'
            };
            document.addEventListener('DOMContentLoaded', function() {
                var flagEl = document.getElementById('current-lang-flag');
                if (flagEl && flags[currentLang]) {
                    flagEl.outerHTML = flags[currentLang].replace('class="', 'id="current-lang-flag" class="');
                }
            });
        })();

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#lang-toggle') && !e.target.closest('#lang-dropdown')) {
                document.getElementById('lang-dropdown').classList.add('hidden');
            }
        });

        /**
         * Google Translate scans the DOM only once. This re-applies it to text that
         * is injected AFTER load — SweetAlert dialogs, dynamically-added product
         * variation rows, etc. — so the whole UI gets translated, not just the
         * markup that existed at first paint.
         *
         * Data-entry areas are deliberately shielded: form fields are ignored by
         * Google Translate already, and the rich-text editor (CKEditor) is marked
         * `notranslate` so the admin's actual content is never altered.
         */
        (function () {
            function activeLang() {
                var m = document.cookie.match(/googtrans=\/auto\/([a-z]+)/);
                return m ? m[1] : null;
            }

            function shieldEditors() {
                document.querySelectorAll('.ck-editor, .ck-content, [contenteditable="true"]').forEach(function (el) {
                    el.classList.add('notranslate');
                    el.setAttribute('translate', 'no');
                });
            }

            var suppress = false;

            function retranslate() {
                var lang = activeLang();
                if (! lang || lang === 'id') return;

                var combo = document.querySelector('.goog-te-combo');
                if (! combo) return;

                suppress = true;
                shieldEditors();
                combo.value = lang;
                combo.dispatchEvent(new Event('change'));
                setTimeout(function () { suppress = false; }, 1200);
            }

            var timer = null;

            function onMutations(mutations) {
                if (suppress) return;

                for (var i = 0; i < mutations.length; i++) {
                    var t = mutations[i].target;
                    if (
                        t && t.nodeType === 1 && t.closest &&
                        t.closest('.notranslate, .ck-editor, [contenteditable], .goog-te-menu-frame, #google_translate_element')
                    ) {
                        continue;
                    }

                    clearTimeout(timer);
                    timer = setTimeout(retranslate, 400);
                    return;
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                shieldEditors();
                new MutationObserver(onMutations).observe(document.body, { childList: true, subtree: true });
            });
        })();

        @yield('scripts')
    </script>
    @stack('scripts-bottom')

    <!-- Google Translate (same approach as the storefront front page) -->
    <div id="google_translate_element" style="display:none"></div>
    <script>
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'id',
                includedLanguages: 'en,id',
                autoDisplay: false
            }, 'google_translate_element');
        }
    </script>
    <script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async defer></script>
</body>
</html>
