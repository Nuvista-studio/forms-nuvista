<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.app-layout')] class extends Component {}; ?>

<div class="max-w-7xl mx-auto px-4 py-6 space-y-4" x-data="columnManager()" x-init="init(['status','nomor_form','tipe','teknisi','pengguna','perangkat','no_asset','kondisi','disetujui','tanggal','aksi'])" @form-deleted.window="window.location.reload()">
    <h1 class="text-2xl font-bold text-primary">Cari & Filter Form</h1>

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Sidebar --}}
        <aside class="w-full lg:w-56 shrink-0">
            <div class="glass-card p-2 sticky top-24">
                <div class="px-3 py-2 mb-1">
                    <h2 class="text-sm font-bold text-primary">Status Form</h2>
                </div>
                <nav class="space-y-0.5">
                    <button wire:click="$set('sidebarFilter', 'all')"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 w-full text-left {{ $sidebarFilter === 'all' ? 'admin-nav-active' : '' }}"
                        style="{{ $sidebarFilter !== 'all' ? 'color: var(--color-text-secondary);' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Semua
                    </button>
                    <button wire:click="$set('sidebarFilter', 'draft_submitted')"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 w-full text-left {{ $sidebarFilter === 'draft_submitted' ? 'admin-nav-active' : '' }}"
                        style="{{ $sidebarFilter !== 'draft_submitted' ? 'color: var(--color-text-secondary);' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Draft & Submitted
                    </button>
                    <button wire:click="$set('sidebarFilter', 'diperiksa')"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 w-full text-left {{ $sidebarFilter === 'diperiksa' ? 'admin-nav-active' : '' }}"
                        style="{{ $sidebarFilter !== 'diperiksa' ? 'color: var(--color-text-secondary);' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Diperiksa
                    </button>
                    <button wire:click="$set('sidebarFilter', 'diketahui')"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 w-full text-left {{ $sidebarFilter === 'diketahui' ? 'admin-nav-active' : '' }}"
                        style="{{ $sidebarFilter !== 'diketahui' ? 'color: var(--color-text-secondary);' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Diketahui
                    </button>
                    <button wire:click="$set('sidebarFilter', 'disetujui')"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 w-full text-left {{ $sidebarFilter === 'disetujui' ? 'admin-nav-active' : '' }}"
                        style="{{ $sidebarFilter !== 'disetujui' ? 'color: var(--color-text-secondary);' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Disetujui
                    </button>
                    <button wire:click="$set('sidebarFilter', 'selesai')"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 w-full text-left {{ $sidebarFilter === 'selesai' ? 'admin-nav-active' : '' }}"
                        style="{{ $sidebarFilter !== 'selesai' ? 'color: var(--color-text-secondary);' : '' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Selesai
                    </button>
                </nav>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 min-w-0 space-y-4">

    {{-- Filters --}}
    <div class="glass-card p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Search --}}
            <div class="lg:col-span-2">
                <label class="text-xs text-muted">Cari</label>
                <input type="text" wire:model.live="search" placeholder="No. form, nama teknisi, nama perangkat..."
                    class="glass-input w-full rounded-lg px-3 py-2 text-sm mt-1">
            </div>

            {{-- Form Type --}}
            <div>
                <label class="text-xs text-muted">Tipe Form</label>
                <select wire:model.live="formType" class="glass-input w-full rounded-lg px-3 py-2 text-sm mt-1">
                    <option value="">Semua</option>
                    <option value="pemeriksaan">Pemeriksaan</option>
                    <option value="perawatan">Perawatan</option>
                </select>
            </div>

            {{-- Status --}}
            <div>
                <label class="text-xs text-muted">Status</label>
                <select wire:model.live="status" class="glass-input w-full rounded-lg px-3 py-2 text-sm mt-1">
                    <option value="">Semua</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="diketahui">Diketahui</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="selesai">Selesai</option>
                    <option value="revisi">Revisi</option>
                </select>
            </div>

            {{-- Kondisi --}}
            <div>
                <label class="text-xs text-muted">Kondisi</label>
                <select wire:model.live="kondisi" class="glass-input w-full rounded-lg px-3 py-2 text-sm mt-1">
                    <option value="">Semua</option>
                    <option value="baru">Baru</option>
                    <option value="lama">Lama</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                    <option value="critical">Critical</option>
                    <option value="poor">Poor</option>
                </select>
            </div>

            {{-- User --}}
            <div class="relative">
                <label class="text-xs text-muted">Teknisi</label>
                <input type="text" wire:model.live="userSearch" wire:input="searchUser"
                    placeholder="Cari nama / NIK..."
                    class="glass-input w-full rounded-lg px-3 py-2 text-sm mt-1"
                    @focus="$wire.set('showUserDropdown', true)"
                    @click.away="$wire.set('showUserDropdown', false)">
                @if($userId)
                    <button wire:click="selectUser()" class="absolute right-2 top-7 text-xs text-red-400">clear</button>
                @endif
                @if($showUserDropdown && count($userResults) > 0)
                    <div class="absolute z-20 w-full mt-1 rounded-lg max-h-40 overflow-y-auto"
                        style="background: var(--color-card-bg); border: 1px solid var(--color-card-border);">
                        @foreach($userResults as $user)
                            <button wire:click="selectUser({{ $user['id'] }})"
                                class="w-full text-left px-3 py-2 text-sm transition-colors text-primary" onmouseover="this.style.backgroundColor='var(--color-bg-tertiary)'" onmouseout="this.style.backgroundColor=''">
                                {{ $user['name'] }} <span class="text-muted text-xs">({{ $user['nik'] ?? '-' }})</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Date From --}}
            <div>
                <label class="text-xs text-muted">Dari Tanggal</label>
                <input type="date" wire:model.live="dateFrom"
                    class="glass-input w-full rounded-lg px-3 py-2 text-sm mt-1">
            </div>

            {{-- Date To --}}
            <div>
                <label class="text-xs text-muted">Sampai Tanggal</label>
                <input type="date" wire:model.live="dateTo"
                    class="glass-input w-full rounded-lg px-3 py-2 text-sm mt-1">
            </div>

            {{-- Reset --}}
            <div class="flex items-end">
                <button wire:click="resetFilters"
                    class="glass-button-secondary text-sm w-full">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    {{-- Results --}}
    <div class="glass-card p-4">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm text-muted">Menampilkan {{ count($results) }} form</p>
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
        </div>

        @if(count($results) === 0)
            <div class="text-center py-12">
                <svg class="w-12 h-12 mx-auto mb-3" style="color: var(--color-text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-sm text-muted">Tidak ada form ditemukan</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b whitespace-nowrap" style="border-color: var(--color-border);">
                            <th x-show="isVisible('status')" wire:click="toggleSort('status')" class="text-left py-2 px-3 text-xs text-muted font-medium cursor-pointer hover:text-primary whitespace-nowrap">
                                Status @if($sortBy === 'status') {{ $sortDir === 'asc' ? '↑' : '↓' }} @endif
                            </th>
                            <th x-show="isVisible('nomor_form')" wire:click="toggleSort('nomor_form')" class="text-left py-2 px-3 text-xs text-muted font-medium cursor-pointer hover:text-primary whitespace-nowrap">
                                No. Form @if($sortBy === 'nomor_form') {{ $sortDir === 'asc' ? '↑' : '↓' }} @endif
                            </th>
                            <th x-show="isVisible('tipe')" class="text-left py-2 px-3 text-xs text-muted font-medium whitespace-nowrap">Tipe</th>
                            <th x-show="isVisible('teknisi')" class="text-left py-2 px-3 text-xs text-muted font-medium whitespace-nowrap">Teknisi</th>
                            <th x-show="isVisible('pengguna')" class="text-left py-2 px-3 text-xs text-muted font-medium whitespace-nowrap">Pengguna</th>
                            <th x-show="isVisible('perangkat')" class="text-left py-2 px-3 text-xs text-muted font-medium whitespace-nowrap">Perangkat</th>
                            <th x-show="isVisible('no_asset')" class="text-left py-2 px-3 text-xs text-muted font-medium whitespace-nowrap">No. Asset</th>
                            <th x-show="isVisible('kondisi')" class="text-left py-2 px-3 text-xs text-muted font-medium whitespace-nowrap">Kondisi</th>
                            <th x-show="isVisible('disetujui')" class="text-left py-2 px-3 text-xs text-muted font-medium whitespace-nowrap">Disetujui</th>
                            <th x-show="isVisible('tanggal')" wire:click="toggleSort('submitted_at')" class="text-left py-2 px-3 text-xs text-muted font-medium cursor-pointer hover:text-primary whitespace-nowrap">
                                Tanggal @if($sortBy === 'submitted_at') {{ $sortDir === 'asc' ? '↑' : '↓' }} @endif
                            </th>
                            <th x-show="isVisible('aksi')" class="text-left py-2 px-3 text-xs text-muted font-medium whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--color-border);">
                        @foreach($results as $form)
                            <tr class="transition-colors whitespace-nowrap" onmouseover="this.style.backgroundColor='var(--color-bg-tertiary)'" onmouseout="this.style.backgroundColor=''">
                                <td x-show="isVisible('status')" class="py-2.5 px-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $this->getStatusColor($form['status']) }}">
                                        {{ ucfirst($form['status']) }}
                                    </span>
                                </td>
                                <td x-show="isVisible('nomor_form')" class="py-2.5 px-3 font-mono font-semibold text-primary text-xs whitespace-nowrap">{{ $form['nomor_form'] }}</td>
                                <td x-show="isVisible('tipe')" class="py-2.5 px-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ $form['type'] === 'pemeriksaan' ? 'bg-blue-500/15 text-blue-400' : 'bg-purple-500/15 text-purple-400' }}">
                                        {{ ucfirst($form['type']) }}
                                    </span>
                                </td>
                                <td x-show="isVisible('teknisi')" class="py-2.5 px-3 text-primary whitespace-nowrap">{{ $form['teknisi'] }}</td>
                                <td x-show="isVisible('pengguna')" class="py-2.5 px-3 text-primary whitespace-nowrap">{{ $form['pengguna'] }}</td>
                                <td x-show="isVisible('perangkat')" class="py-2.5 px-3 text-primary whitespace-nowrap">{{ $form['perangkat'] }}</td>
                                <td x-show="isVisible('no_asset')" class="py-2.5 px-3 font-mono text-xs text-primary whitespace-nowrap">
                                    @if($form['asset_id'])
                                        <a href="{{ route('assets.show', $form['asset_id']) }}" wire:navigate class="hover:underline">{{ $form['no_asset'] }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td x-show="isVisible('kondisi')" class="py-2.5 px-3 text-xs text-secondary whitespace-nowrap">{{ $form['kondisi'] }}</td>
                                <td x-show="isVisible('disetujui')" class="py-2.5 px-3 text-primary whitespace-nowrap">{{ $form['disetujui'] }}</td>
                                <td x-show="isVisible('tanggal')" class="py-2.5 px-3 text-muted text-xs whitespace-nowrap">{{ $form['submitted_at_formatted'] }}</td>
                                <td x-show="isVisible('aksi')" class="py-2.5 px-3 whitespace-nowrap">
                                    <div class="flex items-center gap-1">
                                        <button wire:click="viewForm({{ $form['id'] }}, '{{ $form['type'] }}')"
                                            class="text-xs text-blue-400 hover:underline">View</button>
                                        @if($form['status'] === 'submitted')
                                            <a href="{{ route($form['type'] . '.signature', $form['id']) }}"
                                                wire:navigate
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/15 text-amber-400 hover:bg-amber-500/25 transition-colors ml-1"
                                                title="Tanda Tangan - Diperiksa Oleh">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                Sign
                                            </a>
                                        @endif
                                        @if($form['status'] === 'draft' || $form['status'] === 'revisi')
                                            <a href="{{ route($form['type'] . '.create') }}?formId={{ $form['id'] }}"
                                                wire:navigate
                                                class="text-xs text-yellow-400 hover:underline ml-2">Edit</a>
                                        @endif
                                        @if($form['status'] !== 'draft')
                                            <a href="{{ route('approval.show', ['type' => $form['type'], 'id' => $form['id']]) }}"
                                                wire:navigate
                                                class="text-xs text-blue-400 hover:underline ml-2">Review</a>
                                        @endif
                                        <a href="{{ route($form['type'] . '.export-pdf', $form['id']) }}"
                                            target="_blank"
                                            class="text-xs text-emerald-400 hover:underline ml-2">PDF</a>
                                        <button wire:click="deleteForm({{ $form['id'] }}, '{{ $form['type'] }}')"
                                            wire:confirm="Yakin ingin menghapus form ini? Form yang sudah dihapus tidak dapat dikembalikan."
                                            class="text-red-400 hover:text-red-300 transition-colors ml-2"
                                            title="Hapus Form">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- View Modal --}}
    @if($viewingForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);"
            x-data x-on:click.self="$wire.closeView()">
            <div class="glass-card w-full max-w-2xl max-h-[80vh] overflow-y-auto p-6 space-y-4"
                x-on:click.self="$wire.closeView()">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-primary">Detail Form</h2>
                    <button wire:click="closeView" class="text-muted hover:text-primary text-xl">&times;</button>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-muted text-xs">Nomor Form</span><p class="text-primary font-mono">{{ $viewingForm['nomor_form'] }}</p></div>
                    <div><span class="text-muted text-xs">Tipe</span><p class="text-primary">{{ ucfirst($viewingForm['type']) }}</p></div>
                    <div><span class="text-muted text-xs">Status</span><p class="text-primary">{{ ucfirst($viewingForm['status']) }}</p></div>
                    <div><span class="text-muted text-xs">Tanggal</span><p class="text-primary">{{ $viewingForm['submitted_at'] ? \Carbon\Carbon::parse($viewingForm['submitted_at'])->format('d M Y H:i') : '-' }}</p></div>
                    <div><span class="text-muted text-xs">Teknisi</span><p class="text-primary">{{ $viewingForm['teknisi']['name'] ?? '-' }}</p></div>
                    <div><span class="text-muted text-xs">Pengguna</span><p class="text-primary">{{ $viewingForm['pengguna']['name'] ?? '-' }}</p></div>
                    <div><span class="text-muted text-xs">Perangkat</span><p class="text-primary">{{ $viewingForm['asset']['nama_perangkat'] ?? '-' }}</p></div>
                    <div><span class="text-muted text-xs">No. Asset</span><p class="text-primary font-mono">{{ $viewingForm['asset']['no_asset'] ?? '-' }}</p></div>
                </div>

                @if(!empty($viewingForm['notes']))
                    <div class="text-sm"><span class="text-muted text-xs">Catatan</span><p class="text-primary">{{ $viewingForm['notes'] }}</p></div>
                @endif

                @if(!empty($viewingForm['items']) && count($viewingForm['items']) > 0)
                    <div class="text-sm">
                        <span class="text-muted text-xs font-semibold uppercase">Checklist Items</span>
                        <div class="mt-2 space-y-1">
                            @foreach($viewingForm['items'] as $item)
                                <div class="flex items-center justify-between py-1 px-2 rounded" style="background: var(--color-bg-tertiary);">
                                    <span class="text-primary text-xs">{{ $item['name'] ?? $item['item_name'] ?? '-' }}</span>
                                    @php
                                        $statusClass = match($item['status'] ?? null) {
                                            'baik', 'ok' => 'text-emerald-400',
                                            'tidak_baik', 'not_ok' => 'text-red-400',
                                            default => 'text-muted'
                                        };
                                    @endphp
                                    <span class="text-xs {{ $statusClass }}">{{ ucfirst($item['status'] ?? '-') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!empty($viewingForm['approvals']) && count($viewingForm['approvals']) > 0)
                    <div class="text-sm">
                        <span class="text-muted text-xs font-semibold uppercase">Approval History</span>
                        <div class="mt-2 space-y-1">
                            @foreach($viewingForm['approvals'] as $approval)
                                <div class="flex items-center justify-between py-1 px-2 rounded text-xs" style="background: var(--color-bg-tertiary);">
                                    <span class="text-primary">{{ ucfirst(str_replace('_', ' ', $approval['approval_level'])) }} &mdash; {{ $approval['user']['name'] ?? '-' }}</span>
                                    <span class="text-muted">{{ $approval['status'] }} {{ $approval['approved_at'] ? \Carbon\Carbon::parse($approval['approved_at'])->format('d/m/Y') : '' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route($viewingForm['type'] . '.export-pdf', $viewingForm['id']) }}" target="_blank"
                        class="glass-button-primary text-sm">Export PDF</a>
                    @if($viewingForm['status'] === 'draft' || $viewingForm['status'] === 'revisi')
                        <a href="{{ route($viewingForm['type'] . '.create') }}?formId={{ $viewingForm['id'] }}"
                            wire:navigate class="glass-button-primary text-sm">Edit</a>
                    @endif
                </div>
            </div>
        </div>
    @endif
    </div> {{-- End Main Content --}}
    </div> {{-- End Flex Container --}}
</div>

@push('scripts')
<script>
function columnManager() {
    return {
        columns: [],
        init(defaults) {
            const stored = localStorage.getItem('search_columns');
            if (stored) {
                try { this.columns = JSON.parse(stored); } catch(e) { this.columns = this.defaultColumns(defaults); }
            } else {
                this.columns = this.defaultColumns(defaults);
            }
        },
        defaultColumns(keys) {
            const labels = {
                status: 'Status', nomor_form: 'No. Form', tipe: 'Tipe',
                teknisi: 'Teknisi', pengguna: 'Pengguna', perangkat: 'Perangkat',
                no_asset: 'No. Asset', kondisi: 'Kondisi', disetujui: 'Disetujui',
                tanggal: 'Tanggal', aksi: 'Aksi'
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
        save() { localStorage.setItem('search_columns', JSON.stringify(this.columns)); },
        resetColumns() {
            localStorage.removeItem('search_columns');
            this.columns = this.defaultColumns(['status','nomor_form','tipe','teknisi','pengguna','perangkat','no_asset','kondisi','disetujui','tanggal','aksi']);
        }
    };
}
</script>
@endpush
