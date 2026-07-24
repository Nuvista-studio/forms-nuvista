<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-primary">Data Users</h1>
            <p class="text-sm text-muted mt-1">Daftar seluruh pengguna sistem</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-sm text-muted">{{ $users->total() }} user</span>
            <a href="{{ route('users.export.csv') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200"
                style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export CSV
            </a>
            <a href="{{ route('users.import') }}" wire:navigate
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200"
                style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Import CSV
            </a>
            <a href="{{ route('users.create') }}" wire:navigate
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider cursor-pointer hover:text-secondary transition-colors"
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden sm:table-cell">NIK</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden md:table-cell">Department</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden lg:table-cell">Business Unit</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden lg:table-cell">Site</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--color-border);">
                        @foreach($users as $user)
                            <tr class="transition-colors duration-150" style="hover: background: var(--color-glass-bg);">
                                <td class="px-4 py-3">
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
                                <td class="px-4 py-3 font-mono text-secondary hidden sm:table-cell">{{ $user->nik ?? '-' }}</td>
                                <td class="px-4 py-3 text-secondary hidden md:table-cell">{{ $user->department ?? '-' }}</td>
                                <td class="px-4 py-3 text-secondary hidden lg:table-cell">{{ $user->business_unit ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @forelse($user->roles as $role)
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $this->getRoleBadge($role->name) }}">
                                            {{ $this->getRoleLabel($role->name) }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-muted">-</span>
                                    @endforelse
                                </td>
                                <td class="px-4 py-3 text-secondary hidden lg:table-cell">{{ $user->site ?? '-' }}</td>
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
</div>
