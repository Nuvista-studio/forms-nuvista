<?php

use Livewire\Attributes\Layout;

new #[Layout('components.app-layout')] class extends Component {}; ?>

<div class="max-w-7xl mx-auto px-4 py-6 space-y-4" x-data @form-deleted.window="window.location.reload()">
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
                            <tr class="transition-colors" onmouseover="this.style.backgroundColor='var(--color-bg-tertiary)'" onmouseout="this.style.backgroundColor=''">
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
                                    @if($form['asset_id'])
                                        <a href="{{ route('assets.show', $form['asset_id']) }}" wire:navigate class="hover:underline">{{ $form['no_asset'] }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="py-2.5 text-xs text-secondary">{{ $form['kondisi'] }}</td>
                                <td class="py-2.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $this->getStatusColor($form['status']) }}">
                                        {{ ucfirst($form['status']) }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-muted text-xs">{{ $form['submitted_at_formatted'] }}</td>
                                <td class="py-2.5">
                                    <div class="flex items-center gap-1">
                                        <button wire:click="viewForm({{ $form['id'] }}, '{{ $form['type'] }}')"
                                            class="text-xs text-blue-400 hover:underline">View</button>
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
                                            class="text-xs text-red-400 hover:underline ml-2">Hapus</button>
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
</div>
