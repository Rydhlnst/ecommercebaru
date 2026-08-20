<x-admin::layouts>
    <div class="flex gap-4 max-lg:flex-col">
        <div class="flex flex-1 flex-col gap-4">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                </div>
                <div class="flex gap-2">
                    <a
                        href="{{ route('admin.products.edit', $product->id) }}"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        Edit
                    </a>
                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold
                        {{ $product->isActive() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $product->isActive() ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <!-- Product Info -->
                <div class="col-span-2">
                    <x-admin::card>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Informasi Produk</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-gray-500">SKU</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $product->sku }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">URL Key</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $product->slug }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Harga</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ core()->formatPrice($product->price) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Harga Spesial</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    {{ $product->specialPrice ? core()->formatPrice($product->specialPrice) : '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Stok</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $product->quantity }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Visibilitas</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ ucfirst($product->visibility) }}</dd>
                            </div>
                        </dl>
                    </x-admin::card>

                    <!-- Description -->
                    <x-admin::card>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Deskripsi</h3>
                        <div class="prose prose-sm max-w-none text-gray-700">
                            {!! $product->description ?? '<p class="text-gray-500">Tidak ada deskripsi</p>' !!}
                        </div>
                    </x-admin::card>

                    <!-- SEO -->
                    <x-admin::card>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">SEO</h3>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm text-gray-500">Meta Title</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $product->metaTitle ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">Meta Description</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $product->metaDescription ?? '-' }}</dd>
                            </div>
                        </dl>
                    </x-admin::card>
                </div>

                <!-- Sidebar -->
                <div class="flex flex-col gap-4">
                    <!-- Images -->
                    <x-admin::card>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Gambar</h3>
                        <div class="grid grid-cols-2 gap-2">
                            @forelse($product->images as $image)
                                <div class="aspect-square overflow-hidden rounded-lg bg-gray-100">
                                    <img
                                        src="{{ asset('storage/' . $image['path']) }}"
                                        alt="{{ $product->name }}"
                                        class="h-full w-full object-contain p-1"
                                    />
                                </div>
                            @empty
                                <div class="col-span-2 flex h-32 items-center justify-center rounded-lg border-2 border-dashed border-gray-300">
                                    <p class="text-sm text-gray-500">Tidak ada gambar</p>
                                </div>
                            @endforelse
                        </div>
                    </x-admin::card>

                    <!-- Categories -->
                    <x-admin::card>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Kategori</h3>
                        <div class="space-y-2">
                            @forelse($product->categories as $category)
                                <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-sm text-gray-800">
                                    {{ $category->name }}
                                </span>
                            @empty
                                <p class="text-sm text-gray-500">Tidak ada kategori</p>
                            @endforelse
                        </div>
                    </x-admin::card>

                    <!-- Activity Log -->
                    <x-admin::card>
                        <h3 class="mb-4 text-lg font-semibold text-gray-900">Aktivitas</h3>
                        <div class="space-y-3">
                            @forelse($activityLog->take(5) as $log)
                                <div class="flex gap-3">
                                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-gray-100">
                                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-900">{{ ucfirst($log['action']) }}</p>
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($log['created_at'])->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Belum ada aktivitas</p>
                            @endforelse
                        </div>
                    </x-admin::card>
                </div>
            </div>
        </div>
    </div>
</x-admin::layouts>
