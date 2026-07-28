<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        Permissions
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">Permissions Matrix</h1>
            <button
                onclick="seedRoles()"
                class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
            >
                Seed Default Roles
            </button>
        </div>

        <!-- Roles Overview -->
        <div class="mb-8">
            <h2 class="mb-4 text-xl font-semibold text-gray-900">Roles</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                @foreach($roles as $slug => $role)
                    <div class="rounded-lg border border-gray-200 bg-white p-4">
                        <h3 class="font-medium text-gray-900">{{ $role['name'] }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $role['description'] }}</p>
                        <div class="mt-2">
                            @if(in_array('*', $role['permissions']))
                                <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">
                                    Full Access
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-800">
                                    {{ count($role['permissions']) }} permissions
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Permissions Matrix -->
        <div>
            <h2 class="mb-4 text-xl font-semibold text-gray-900">Permissions Matrix</h2>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Module</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Permission</th>
                            @foreach($roles as $slug => $role)
                                <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                    {{ $role['name'] }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($permissions as $module => $items)
                            @foreach($items as $permission => $label)
                                <tr>
                                    @if($loop->first)
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900" rowspan="{{ count($items) }}">
                                            {{ ucfirst(str_replace('_', ' ', $module)) }}
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $label }}
                                    </td>
                                    @foreach($roles as $slug => $role)
                                        <td class="px-4 py-4 text-center">
                                            @if(in_array('*', $role['permissions']) || in_array($module . '.' . $permission, $role['permissions']))
                                                <svg class="mx-auto h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 14.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            @else
                                                <svg class="mx-auto h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function seedRoles() {
                if (!confirm('Ini akan membuat default roles. Lanjutkan?')) {
                    return;
                }

                fetch('{{ route("admin.permissions.index") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        </script>
    @endpush
</x-admin::layouts>
