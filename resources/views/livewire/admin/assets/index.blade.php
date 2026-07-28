<div class="space-y-6"
    x-data x-on:asset-deleted.window="$wire.$refresh()" x-on:asset-created.window="window.location = '{{ route('admin.assets.index') }}'" x-on:asset-updated.window="window.location = '{{ route('admin.assets.index') }}'">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-primary">Data Assets</h1>
            <p class="text-sm text-muted mt-1">Daftar seluruh perangkat / asset</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-sm text-muted">{{ $assets->total() }} asset</span>
            <a href="{{ route('admin.assets.export.csv') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200"
                style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export CSV
            </a>
            <a href="{{ route('admin.assets.import') }}" wire:navigate
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200"
                style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import CSV
            </a>
            <a href="{{ route('admin.assets.create') }}" wire:navigate
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200"
                style="background: var(--color-primary); color: var(--color-button-text);">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Asset
            </a>
        </div>
    </div>

    {{-- Search --}}
    <div class="glass-card p-4">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Cari no asset, nama perangkat, kategori, brand..."
                class="w-full pl-10 pr-4 py-2 rounded-lg text-sm transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
            />
        </div>
    </div>

    {{-- Assets Table --}}
    @if($assets->count() > 0)
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b" style="border-color: var(--color-border);">
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">No Asset</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Nama Perangkat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden sm:table-cell">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden md:table-cell">Brand / Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden lg:table-cell">No Serial</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden xl:table-cell">Operating Unit</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden xl:table-cell">Site (Location)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden lg:table-cell">Pengguna</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--color-border);">
                        @foreach($assets as $a)
                            <tr class="transition-colors duration-150 cursor-pointer" style="hover: background: var(--color-glass-bg);" onclick="window.location='{{ route('admin.assets.edit', $a->id) }}'">
                                <td class="px-4 py-3 font-mono text-secondary whitespace-nowrap">{{ $a->no_asset }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-medium text-primary truncate max-w-[180px]">{{ $a->nama_perangkat }}</div>
                                    @if($a->brand)
                                        <div class="text-xs text-muted mt-0.5 truncate max-w-[180px]">{{ $a->brand }} {{ $a->tipe }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-secondary hidden sm:table-cell whitespace-nowrap">{{ $a->kategori }}</td>
                                <td class="px-4 py-3 text-secondary hidden md:table-cell whitespace-nowrap">{{ $a->brand }} {{ $a->tipe }}</td>
                                <td class="px-4 py-3 text-secondary hidden lg:table-cell font-mono text-xs whitespace-nowrap">{{ $a->no_serial ?? '-' }}</td>
                                <td class="px-4 py-3 hidden xl:table-cell whitespace-nowrap">
                                    @if($a->operatingUnitSite)
                                        <div class="font-medium text-primary truncate max-w-[150px]">{{ $a->operatingUnitSite->site }}</div>
                                        @if($a->operatingUnitSite->country)
                                            <div class="text-xs text-muted mt-0.5 truncate max-w-[150px]">{{ $a->operatingUnitSite->country }}</div>
                                        @endif
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 hidden xl:table-cell whitespace-nowrap">
                                    @if($a->siteAsset)
                                        <div class="font-medium text-primary truncate max-w-[150px]">{{ $a->siteAsset->site }}</div>
                                        @if($a->siteAsset->city)
                                            <div class="text-xs text-muted mt-0.5 truncate max-w-[150px]">{{ $a->siteAsset->city }}</div>
                                        @endif
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-secondary hidden lg:table-cell">{{ $a->assignedUser->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($a->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background: rgba(34,197,94,0.15); color: #22c55e;">Active</span>
                                    @elseif($a->status === 'disposed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background: rgba(239,68,68,0.15); color: #ef4444;">Disposed</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background: rgba(234,179,8,0.15); color: #eab308;">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right" onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.assets.edit', $a->id) }}" wire:navigate
                                            class="p-1.5 rounded-lg transition-colors duration-200"
                                            style="color: var(--color-text-secondary);"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <button wire:click="confirmDelete('{{ $a->id }}', '{{ addslashes($a->nama_perangkat) }}')"
                                            class="p-1.5 rounded-lg transition-colors duration-200 text-red-400 hover:text-red-300"
                                            title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $assets->links() }}
        </div>
    @else
        <div class="glass-card p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="mt-3 text-muted">Tidak ada asset ditemukan</p>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);"
            x-data x-on:keydown.escape.window="$wire.cancelDelete()">
            <div class="glass-card p-6 w-full max-w-md space-y-4" @click.away="$wire.cancelDelete()">
                <h3 class="text-lg font-bold text-primary">Hapus Asset</h3>
                <p class="text-sm text-muted">Yakin ingin menghapus asset <span class="font-semibold text-primary">{{ $deleteAssetName }}</span>?</p>
                <div class="flex gap-2">
                    <button wire:click="cancelDelete" type="button" class="glass-button-secondary text-sm flex-1">Batal</button>
                    <button wire:click="deleteAsset" type="button" class="flex-1 px-4 py-2 rounded-lg font-medium text-sm bg-red-500 text-white hover:bg-red-600 transition-all duration-200">Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>
