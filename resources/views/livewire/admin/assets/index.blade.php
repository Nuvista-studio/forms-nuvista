<div class="space-y-6" x-data="columnManager()" x-init="init(['status','no_asset','nama_perangkat','kategori','brand_tipe','no_serial','operating_unit','site_location','pengguna','aksi'])" x-on:asset-deleted.window="$wire.$refresh()" x-on:asset-created.window="window.location = '{{ route('admin.assets.index') }}'" x-on:asset-updated.window="window.location = '{{ route('admin.assets.index') }}'">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-primary">Data Assets</h1>
            <p class="text-sm text-muted mt-1">Daftar seluruh perangkat / asset</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-sm text-muted">{{ $assets->total() }} asset</span>
            {{-- Column Manager --}}
            <div class="relative" x-data="{ colOpen: false }">
                <button @click="colOpen = !colOpen"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200"
                    style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 4h6m-3 12v6m-7-4l4-4m0 0l4 4m-4-4V4"/>
                    </svg>
                    Columns
                </button>
                <div x-show="colOpen" @click.away="colOpen = false" x-cloak
                    class="absolute right-0 mt-1 w-56 rounded-lg shadow-lg z-30 py-2"
                    style="background: var(--color-card-bg); border: 1px solid var(--color-card-border);"
                    x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                    <div class="px-3 pb-1.5 text-[10px] font-semibold text-muted uppercase tracking-wider">Show / Hide Columns</div>
                    <template x-for="(col, idx) in columns" :key="col.key">
                        <label class="flex items-center gap-2 px-3 py-1.5 text-xs cursor-pointer hover:bg-[var(--color-bg-tertiary)] transition-colors">
                            <span @mousedown.prevent @mousedown="startDrag(idx, $event)" class="cursor-grab active:cursor-grabbing text-muted hover:text-primary shrink-0" title="Drag to reorder">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 6h2v2H8V6zm6 0h2v2h-2V6zM8 11h2v2H8v-2zm6 0h2v2h-2v-2zm-6 5h2v2H8v-2zm6 0h2v2h-2v-2z"/></svg>
                            </span>
                            <input type="checkbox" x-model="col.visible" class="rounded border-gray-400 text-blue-500 focus:ring-blue-500">
                            <span x-text="col.label" class="text-primary"></span>
                            <span class="ml-auto text-[10px] text-muted" x-text="idx + 1"></span>
                        </label>
                    </template>
                    <div class="border-t mt-1 pt-1.5 px-3" style="border-color: var(--color-border);">
                        <button @click="resetColumns()" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">Reset Default</button>
                    </div>
                </div>
            </div>
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
                            <th x-show="isVisible('status')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                            <th x-show="isVisible('no_asset')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">No Asset</th>
                            <th x-show="isVisible('nama_perangkat')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Nama Perangkat</th>
                            <th x-show="isVisible('kategori')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden sm:table-cell">Kategori</th>
                            <th x-show="isVisible('brand_tipe')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden md:table-cell">Brand / Tipe</th>
                            <th x-show="isVisible('no_serial')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden lg:table-cell">No Serial</th>
                            <th x-show="isVisible('operating_unit')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden xl:table-cell">Operating Unit</th>
                            <th x-show="isVisible('site_location')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden xl:table-cell">Site (Location)</th>
                            <th x-show="isVisible('pengguna')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden lg:table-cell">Pengguna</th>
                            <th x-show="isVisible('aksi')" class="px-4 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--color-border);">
                        @foreach($assets as $a)
                            <tr class="transition-colors duration-150 cursor-pointer" style="hover: background: var(--color-glass-bg);" onclick="window.location='{{ route('admin.assets.edit', $a->id) }}'">
                                <td x-show="isVisible('status')" class="px-4 py-3">
                                    @if($a->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background: rgba(34,197,94,0.15); color: #22c55e;">Active</span>
                                    @elseif($a->status === 'disposed')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background: rgba(239,68,68,0.15); color: #ef4444;">Disposed</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background: rgba(234,179,8,0.15); color: #eab308;">Inactive</span>
                                    @endif
                                </td>
                                <td x-show="isVisible('no_asset')" class="px-4 py-3 font-mono text-secondary whitespace-nowrap">{{ $a->no_asset }}</td>
                                <td x-show="isVisible('nama_perangkat')" class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-medium text-primary truncate max-w-[180px]">{{ $a->nama_perangkat }}</div>
                                    @if($a->brand)
                                        <div class="text-xs text-muted mt-0.5 truncate max-w-[180px]">{{ $a->brand }} {{ $a->tipe }}</div>
                                    @endif
                                </td>
                                <td x-show="isVisible('kategori')" class="px-4 py-3 text-secondary hidden sm:table-cell whitespace-nowrap">{{ $a->kategori }}</td>
                                <td x-show="isVisible('brand_tipe')" class="px-4 py-3 text-secondary hidden md:table-cell whitespace-nowrap">{{ $a->brand }} {{ $a->tipe }}</td>
                                <td x-show="isVisible('no_serial')" class="px-4 py-3 text-secondary hidden lg:table-cell font-mono text-xs whitespace-nowrap">{{ $a->no_serial ?? '-' }}</td>
                                <td x-show="isVisible('operating_unit')" class="px-4 py-3 hidden xl:table-cell whitespace-nowrap">
                                    @if($a->operatingUnitSite)
                                        <div class="font-medium text-primary truncate max-w-[150px]">{{ $a->operatingUnitSite->site }}</div>
                                        @if($a->operatingUnitSite->country)
                                            <div class="text-xs text-muted mt-0.5 truncate max-w-[150px]">{{ $a->operatingUnitSite->country }}</div>
                                        @endif
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </td>
                                <td x-show="isVisible('site_location')" class="px-4 py-3 hidden xl:table-cell whitespace-nowrap">
                                    @if($a->siteAsset)
                                        <div class="font-medium text-primary truncate max-w-[150px]">{{ $a->siteAsset->site }}</div>
                                        @if($a->siteAsset->city)
                                            <div class="text-xs text-muted mt-0.5 truncate max-w-[150px]">{{ $a->siteAsset->city }}</div>
                                        @endif
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </td>
                                <td x-show="isVisible('pengguna')" class="px-4 py-3 text-secondary hidden lg:table-cell">{{ $a->assignedUser->name ?? '-' }}</td>
                                <td x-show="isVisible('aksi')" class="px-4 py-3 text-right" onclick="event.stopPropagation()">
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

@push('scripts')
<script>
function columnManager() {
    return {
        columns: [],
        init(defaults) {
            const stored = localStorage.getItem('assets_columns');
            if (stored) {
                try { this.columns = JSON.parse(stored); } catch(e) { this.columns = this.defaultColumns(defaults); }
            } else {
                this.columns = this.defaultColumns(defaults);
            }
        },
        defaultColumns(keys) {
            const labels = {
                status: 'Status', no_asset: 'No Asset', nama_perangkat: 'Nama Perangkat',
                kategori: 'Kategori', brand_tipe: 'Brand / Tipe', no_serial: 'No Serial',
                operating_unit: 'Operating Unit', site_location: 'Site (Location)',
                pengguna: 'Pengguna', aksi: 'Aksi'
            };
            return keys.map(k => ({ key: k, label: labels[k] || k, visible: true }));
        },
        isVisible(key) {
            const col = this.columns.find(c => c.key === key);
            return col ? col.visible : true;
        },
        startDrag(idx, event) {
            const dragEl = event.target.closest('label');
            const clone = dragEl.cloneNode(true);
            clone.style.position = 'fixed'; clone.style.pointerEvents = 'none';
            clone.style.opacity = '0.6'; clone.style.zIndex = '1000';
            clone.style.width = dragEl.offsetWidth + 'px';
            document.body.appendChild(clone);
            const offsetY = event.clientY - dragEl.getBoundingClientRect().top;
            const offsetX = event.clientX - dragEl.getBoundingClientRect().left;
            const onMove = (e) => {
                clone.style.left = (e.clientX - offsetX) + 'px';
                clone.style.top = (e.clientY - offsetY) + 'px';
                const items = [...dragEl.closest('div').querySelectorAll('label')];
                const target = items.find(item => {
                    if (item === dragEl) return false;
                    const rect = item.getBoundingClientRect();
                    return e.clientY < rect.top + rect.height / 2;
                });
                if (target) dragEl.parentNode.insertBefore(dragEl, target);
                else { const last = items[items.length - 1]; if (last && last !== dragEl) dragEl.parentNode.appendChild(dragEl); }
            };
            const onUp = () => {
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup', onUp);
                clone.remove();
                const items = [...dragEl.closest('div').querySelectorAll('label')];
                const newOrder = items.map(label => parseInt(label.querySelector('span:last-child')?.textContent || '1') - 1);
                const sorted = newOrder.map(i => this.columns[i]).filter(Boolean);
                if (sorted.length === this.columns.length) this.columns = sorted;
                this.save();
            };
            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
        },
        save() { localStorage.setItem('assets_columns', JSON.stringify(this.columns)); },
        resetColumns() {
            localStorage.removeItem('assets_columns');
            this.columns = this.defaultColumns(['status','no_asset','nama_perangkat','kategori','brand_tipe','no_serial','operating_unit','site_location','pengguna','aksi']);
        }
    };
}
</script>
@endpush
