<?php

use Livewire\Attributes\Layout;

new #[Layout('components.app-layout')] class extends Component {}; ?>

<div class="max-w-7xl mx-auto px-4 py-6 space-y-4">
    <h1 class="text-2xl font-bold text-primary">Cari & Filter Form</h1>

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
                    <option value="good_normal">Good / Normal</option>
                    <option value="caution_poor">Caution / Poor</option>
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
                                class="w-full text-left px-3 py-2 text-sm hover:bg-white/5 transition-colors text-primary">
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
                        <tr class="border-b" style="border-color: var(--color-border);">
                            <th wire:click="toggleSort('nomor_form')" class="text-left py-2 text-xs text-muted font-medium cursor-pointer hover:text-primary">
                                No. Form @if($sortBy === 'nomor_form') {{ $sortDir === 'asc' ? '↑' : '↓' }} @endif
                            </th>
                            <th class="text-left py-2 text-xs text-muted font-medium">Tipe</th>
                            <th class="text-left py-2 text-xs text-muted font-medium">Teknisi</th>
                            <th class="text-left py-2 text-xs text-muted font-medium">Perangkat</th>
                            <th class="text-left py-2 text-xs text-muted font-medium">No. Asset</th>
                            <th class="text-left py-2 text-xs text-muted font-medium">Kondisi</th>
                            <th wire:click="toggleSort('status')" class="text-left py-2 text-xs text-muted font-medium cursor-pointer hover:text-primary">
                                Status @if($sortBy === 'status') {{ $sortDir === 'asc' ? '↑' : '↓' }} @endif
                            </th>
                            <th wire:click="toggleSort('submitted_at')" class="text-left py-2 text-xs text-muted font-medium cursor-pointer hover:text-primary">
                                Tanggal @if($sortBy === 'submitted_at') {{ $sortDir === 'asc' ? '↑' : '↓' }} @endif
                            </th>
                            <th class="text-left py-2 text-xs text-muted font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--color-border);">
                        @foreach($results as $form)
                            <tr class="hover:bg-white/5 transition-colors">
                                <td class="py-2.5 font-mono font-semibold text-primary text-xs">{{ $form['nomor_form'] }}</td>
                                <td class="py-2.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ $form['type'] === 'pemeriksaan' ? 'bg-blue-500/15 text-blue-400' : 'bg-purple-500/15 text-purple-400' }}">
                                        {{ ucfirst($form['type']) }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-primary">{{ $form['teknisi'] }}</td>
                                <td class="py-2.5 text-primary">{{ $form['perangkat'] }}</td>
                                <td class="py-2.5 font-mono text-xs text-primary">
                                    <a href="{{ route('assets.show', $form['asset_id']) }}" wire:navigate class="hover:underline">{{ $form['no_asset'] }}</a>
                                </td>
                                <td class="py-2.5 text-xs text-secondary">{{ $form['kondisi'] }}</td>
                                <td class="py-2.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $getStatusColor($form['status']) }}">
                                        {{ ucfirst($form['status']) }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-muted text-xs">{{ $form['submitted_at_formatted'] }}</td>
                                <td class="py-2.5">
                                    <div class="flex items-center gap-1">
                                        @if($form['status'] !== 'draft')
                                            <a href="{{ route('approval.show', ['type' => $form['type'], 'id' => $form['id']]) }}"
                                                wire:navigate
                                                class="text-xs text-blue-400 hover:underline">Review</a>
                                        @endif
                                        <a href="{{ route($form['type'] . '.export-pdf', $form['id']) }}"
                                            target="_blank"
                                            class="text-xs text-emerald-400 hover:underline ml-2">PDF</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
