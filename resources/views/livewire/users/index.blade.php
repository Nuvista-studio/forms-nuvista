<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6" x-data="columnManager()" x-init="init(['nama','nik','department','business_unit','role','site','aksi'])" x-on:user-deleted.window="$wire.$refresh()" x-on:user-updated.window="$wire.$refresh()">
    {{-- Toast --}}
    @if (session()->has('success'))
        <div class="p-3 rounded-lg text-sm"
            style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #22c55e;">
            {{ session('success') }}
        </div>
    @endif
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-primary">Data Users</h1>
            <p class="text-sm text-muted mt-1">Daftar seluruh pengguna sistem</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-sm text-muted">{{ $users->total() }} user</span>
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
            <a href="{{ route('admin.users.export.csv') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200"
                style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export CSV
            </a>
            <a href="{{ route('admin.users.import') }}" wire:navigate
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200"
                style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import CSV
            </a>
            <a href="{{ route('admin.users.create') }}" wire:navigate
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200"
                style="background: var(--color-primary); color: var(--color-button-text);">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah User
            </a>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="glass-card p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Cari nama, email, NIK, department..."
                        class="w-full pl-10 pr-4 py-2 rounded-lg text-sm transition-colors duration-200"
                        style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                    />
                </div>
            </div>
            <select
                wire:model.live="filterRole"
                class="px-3 py-2 rounded-lg text-sm transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
            >
                <option value="">Semua Role</option>
                @foreach($this->getRoleList() as $role)
                    <option value="{{ $role }}">{{ $this->getRoleLabel($role) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Users Table --}}
    @if($users->count() > 0)
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b" style="border-color: var(--color-border);">
                            <th x-show="isVisible('nama')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider cursor-pointer hover:text-secondary transition-colors"
                                wire:click="toggleSort('name')">
                                <span class="flex items-center gap-1">
                                    Nama
                                    @if($sortBy === 'name')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                                        </svg>
                                    @endif
                                </span>
                            </th>
                            <th x-show="isVisible('nik')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden sm:table-cell">NIK</th>
                            <th x-show="isVisible('department')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden md:table-cell">Department</th>
                            <th x-show="isVisible('business_unit')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden lg:table-cell">Business Unit</th>
                            <th x-show="isVisible('role')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Role</th>
                            <th x-show="isVisible('site')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden lg:table-cell">Site</th>
                            <th x-show="isVisible('aksi')" class="px-4 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--color-border);">
                        @foreach($users as $user)
                            <tr class="transition-colors duration-150" style="hover: background: var(--color-glass-bg);">
                                <td x-show="isVisible('nama')" class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold"
                                             style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-primary);">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-medium text-primary truncate">{{ $user->name }}</div>
                                            <div class="text-xs text-muted truncate">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td x-show="isVisible('nik')" class="px-4 py-3 font-mono text-secondary hidden sm:table-cell">{{ $user->nik ?? '-' }}</td>
                                <td x-show="isVisible('department')" class="px-4 py-3 text-secondary hidden md:table-cell">{{ $user->department ?? '-' }}</td>
                                <td x-show="isVisible('business_unit')" class="px-4 py-3 text-secondary hidden lg:table-cell">{{ $user->business_unit ?? '-' }}</td>
                                <td x-show="isVisible('role')" class="px-4 py-3">
                                    @forelse($user->roles as $role)
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $this->getRoleBadge($role->name) }}">
                                            {{ $this->getRoleLabel($role->name) }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-muted">-</span>
                                    @endforelse
                                </td>
                                <td x-show="isVisible('site')" class="px-4 py-3 text-secondary hidden lg:table-cell">{{ $user->site ?? '-' }}</td>
                                <td x-show="isVisible('aksi')" class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" wire:navigate
                                            class="p-1.5 rounded-lg transition-colors duration-200"
                                            style="color: var(--color-text-secondary);"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <button wire:click="confirmDelete({{ $user->id }}, '{{ addslashes($user->name) }}')"
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
            {{ $users->links() }}
        </div>
    @else
        <div class="glass-card p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="mt-3 text-muted">Tidak ada user ditemukan</p>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);"
            x-data x-on:keydown.escape.window="$wire.cancelDelete()">
            <div class="glass-card p-6 w-full max-w-md space-y-4" @click.away="$wire.cancelDelete()">
                <h3 class="text-lg font-bold text-primary">Hapus User</h3>
                <p class="text-sm text-muted">Yakin ingin menghapus user <span class="font-semibold text-primary">{{ $deleteUserName }}</span>? User akan di-soft-delete dan tidak akan bisa login lagi.</p>
                <div class="flex gap-2">
                    <button wire:click="cancelDelete" type="button" class="glass-button-secondary text-sm flex-1">Batal</button>
                    <button wire:click="deleteUser" type="button" class="flex-1 px-4 py-2 rounded-lg font-medium text-sm bg-red-500 text-white hover:bg-red-600 transition-all duration-200">Hapus</button>
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
            const stored = localStorage.getItem('users_columns');
            if (stored) {
                try { this.columns = JSON.parse(stored); } catch(e) { this.columns = this.defaultColumns(defaults); }
            } else {
                this.columns = this.defaultColumns(defaults);
            }
        },
        defaultColumns(keys) {
            const labels = {
                nama: 'Nama', nik: 'NIK', department: 'Department',
                business_unit: 'Business Unit', role: 'Role', site: 'Site', aksi: 'Aksi'
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
        save() { localStorage.setItem('users_columns', JSON.stringify(this.columns)); },
        resetColumns() {
            localStorage.removeItem('users_columns');
            this.columns = this.defaultColumns(['nama','nik','department','business_unit','role','site','aksi']);
        }
    };
}
</script>
@endpush
