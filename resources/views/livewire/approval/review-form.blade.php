<?php

use Livewire\Attributes\Layout;

new #[Layout('components.app-layout')] class extends Component {}; ?>

<div class="max-w-5xl mx-auto px-4 py-6 space-y-4"
    x-data="{ editing: @entangle('editing') }"
    x-on:edit-saved.window="editing = false">

    {{-- SUCCESS / REJECT STATE --}}
    @if($saved)
        <div class="glass-card p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-emerald-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h2 class="text-xl font-bold text-primary mb-2">Approval Berhasil</h2>
            <p class="text-sm text-muted mb-6">Form {{ $formType === 'pemeriksaan' ? $pemeriksaanForm->nomor_form : $perawatanForm->nomor_form }} telah di-approve.</p>
            <a href="{{ route('dashboard') }}" wire:navigate class="glass-button-primary inline-block">Kembali ke Dashboard</a>
        </div>
    @elseif($rejected)
        <div class="glass-card p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h2 class="text-xl font-bold text-primary mb-2">Form Dikembalikan</h2>
            <p class="text-sm text-muted mb-6">Form {{ $formType === 'pemeriksaan' ? $pemeriksaanForm->nomor_form : $perawatanForm->nomor_form }} telah dikembalikan ke teknisi untuk revisi.</p>
            <a href="{{ route('dashboard') }}" wire:navigate class="glass-button-primary inline-block">Kembali ke Dashboard</a>
        </div>
    @else
        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-primary">Review & Approval</h1>
                <p class="text-sm text-muted mt-1">
                    Approval Level: <span class="font-semibold text-primary">{{ $approvalLevel === 'diketahui_oleh' ? 'Diketahui Oleh' : 'Disetujui Oleh' }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @php $form = $formType === 'pemeriksaan' ? $pemeriksaanForm : $perawatanForm; @endphp
                @if($canApprove && !$editing)
                    <button wire:click="toggleEdit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200"
                        style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </button>
                @elseif($editing)
                    <button wire:click="toggleEdit"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200"
                        style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                        Batal Edit
                    </button>
                    <button wire:click="saveEdits" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200"
                        style="background: var(--color-primary); color: var(--color-button-text);">
                        <span wire:loading.remove wire:target="saveEdits">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                        <span wire:loading wire:target="saveEdits">Menyimpan...</span>
                        <span wire:loading.remove wire:target="saveEdits">Simpan Perubahan</span>
                    </button>
                @endif
                <a href="{{ route($formType . '.export-pdf', $form->id) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200"
                    style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export PDF
                </a>
                @if($form->asset_id)
                <a href="{{ route('assets.show', $form->asset_id) }}" wire:navigate
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200"
                    style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Lihat Aset
                </a>
                @endif
                <span class="px-3 py-1 rounded-full text-xs font-medium
                    {{ $formType === 'pemeriksaan' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' }}">
                    {{ $formType === 'pemeriksaan' ? 'Pemeriksaan' : 'Perawatan' }}
                </span>
            </div>
        </div>

        {{-- FORM SUMMARY --}}
        @php $form = $formType === 'pemeriksaan' ? $pemeriksaanForm : $perawatanForm; @endphp

        {{-- Info Ringkas --}}
        <div class="glass-card p-4">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-xs text-muted">No. Form</span>
                    <p class="font-mono font-semibold text-primary">{{ $form->nomor_form }}</p>
                </div>
                <div>
                    <span class="text-xs text-muted">Status</span>
                    <p class="font-semibold text-primary">{{ ucfirst($form->status) }}</p>
                </div>
                <div>
                    <span class="text-xs text-muted">Teknisi</span>
                    <p class="text-primary">{{ $form->teknisi->name }}</p>
                </div>
                <div>
                    <span class="text-xs text-muted">Tanggal</span>
                    <p class="text-primary">{{ $form->submitted_at ? $form->submitted_at->format('d M Y H:i') : '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Info Pengguna --}}
        <div class="glass-card p-4">
            <h3 class="text-sm font-semibold text-primary mb-3 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Info Pengguna
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                <div>
                    <span class="text-xs text-muted">Pengguna</span>
                    <p class="text-primary">{{ $form->pengguna->name ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-muted">NIK</span>
                    <p class="text-primary">{{ $form->pengguna->nik ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-muted">Department</span>
                    <p class="text-primary">{{ $form->pengguna->department ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Info Perangkat --}}
        <div class="glass-card p-4">
            <h3 class="text-sm font-semibold text-primary mb-3 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Info Perangkat
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                <div>
                    <span class="text-xs text-muted">Kategori</span>
                    <p class="text-primary">{{ $form->asset->kategori ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-muted">Brand</span>
                    <p class="text-primary">{{ $form->asset->brand ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-muted">Tipe</span>
                    <p class="text-primary">{{ $form->asset->tipe ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-muted">Nama Perangkat</span>
                    <p class="text-primary">{{ $form->asset->nama_perangkat ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-muted">No. Serial</span>
                    <p class="font-mono text-primary">{{ $form->asset->no_serial ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-muted">No. Asset</span>
                    <p class="font-mono text-primary">{{ $form->asset->no_asset ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Kondisi --}}
        @if($formType === 'pemeriksaan')
            <div class="glass-card p-4">
                <h3 class="text-sm font-semibold text-primary mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Kondisi
                </h3>
                @if($editing)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div>
                            <label class="text-xs text-muted">Kondisi Perangkat</label>
                            <select wire:model="editKondisi"
                                class="w-full px-3 py-2 rounded-lg text-sm mt-1 transition-colors duration-200"
                                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);">
                                <option value="">Pilih Kondisi</option>
                                <option value="baru">Baru</option>
                                <option value="lama">Lama</option>
                                <option value="good_normal">Good / Normal</option>
                                <option value="caution_poor">Caution / Poor</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-muted">Keterangan</label>
                            <input wire:model="editKondisiKeterangan" type="text"
                                class="w-full px-3 py-2 rounded-lg text-sm mt-1 transition-colors duration-200"
                                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                                placeholder="Keterangan kondisi..." />
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-xs text-muted">Kondisi Perangkat</span>
                            <p class="{{ $this->getStatusColor($form->kondisi ?? '') }}">{{ $this->getStatusLabel($form->kondisi ?? '') }}</p>
                        </div>
                        @if($form->kondisi_keterangan)
                            <div>
                                <span class="text-xs text-muted">Keterangan</span>
                                <p class="text-primary">{{ $form->kondisi_keterangan }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @else
            <div class="glass-card p-4">
                <h3 class="text-sm font-semibold text-primary mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Kondisi Setelah Perawatan
                </h3>
                @if($editing)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div>
                            <label class="text-xs text-muted">Kondisi Akhir</label>
                            <select wire:model="editKondisi"
                                class="w-full px-3 py-2 rounded-lg text-sm mt-1 transition-colors duration-200"
                                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);">
                                <option value="">Pilih Kondisi</option>
                                <option value="good_normal">Good / Normal</option>
                                <option value="caution_poor">Caution / Poor</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-muted">Keterangan</label>
                            <input wire:model="editKondisiKeterangan" type="text"
                                class="w-full px-3 py-2 rounded-lg text-sm mt-1 transition-colors duration-200"
                                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                                placeholder="Keterangan kondisi..." />
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-xs text-muted">Kondisi Akhir</span>
                            <p class="{{ $this->getStatusColor($form->kondisi_akhir ?? '') }}">{{ $this->getStatusLabel($form->kondisi_akhir ?? '') }}</p>
                        </div>
                        @if($form->kondisi_akhir_notes)
                            <div>
                                <span class="text-xs text-muted">Keterangan</span>
                                <p class="text-primary">{{ $form->kondisi_akhir_notes }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        {{-- Checklist Items --}}
        @php
            $categories = $formType === 'pemeriksaan'
                ? ['hardware' => 'Pemeriksaan Hardware', 'aplikasi' => 'Pemeriksaan Aplikasi', 'operating_system' => 'Operating System']
                : ['hardware' => 'Perawatan Hardware', 'aplikasi' => 'Perawatan Aplikasi', 'operating_system' => 'Operating System'];
        @endphp

        @foreach($categories as $catKey => $catLabel)
            @php $items = $form->items->where('category', $catKey)->sortBy('sort_order'); @endphp
            @if($items->count() > 0)
                <div class="glass-card p-4">
                    <h3 class="text-sm font-semibold text-primary mb-3">{{ $catLabel }}</h3>
                    <div class="space-y-2">
                        @foreach($items as $item)
                            @php
                                $editIndex = collect($editItems)->search(fn($ei) => $ei['id'] === $item->id);
                            @endphp
                            @if($editing && $editIndex !== false)
                                <div class="py-2 px-3 rounded-lg space-y-2" style="background: var(--color-bg-secondary);">
                                    <span class="text-sm text-primary font-medium">{{ $item->name }}</span>
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if($formType === 'pemeriksaan' && $item->value !== null)
                                            <input wire:model="editItems.{{ $editIndex }}.value" type="text"
                                                placeholder="Nilai"
                                                class="w-24 px-2 py-1 rounded text-xs transition-colors duration-200"
                                                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);" />
                                        @endif
                                        <select wire:model="editItems.{{ $editIndex }}.status"
                                            class="px-2 py-1 rounded text-xs transition-colors duration-200"
                                            style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);">
                                            <option value="">Pilih Status</option>
                                            <option value="baik">Baik</option>
                                            <option value="tidak_baik">Tidak Baik</option>
                                            <option value="good_normal">Good / Normal</option>
                                            <option value="caution_poor">Caution / Poor</option>
                                            <option value="baru">Baru</option>
                                            <option value="lama">Lama</option>
                                        </select>
                                        <input wire:model="editItems.{{ $editIndex }}.keterangan" type="text"
                                            placeholder="Keterangan"
                                            class="flex-1 min-w-[120px] px-2 py-1 rounded text-xs transition-colors duration-200"
                                            style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);" />
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-between py-2 px-3 rounded-lg" style="background: var(--color-bg-secondary);">
                                    <span class="text-sm text-primary">{{ $item->name }}</span>
                                    <div class="flex items-center gap-3">
                                        @if($item->value)
                                            <span class="text-xs text-muted">{{ $item->value }}</span>
                                        @endif
                                        @if($item->status)
                                            <span class="text-xs font-medium {{ $this->getStatusColor($item->status) }}">{{ $this->getStatusLabel($item->status) }}</span>
                                        @endif
                                        @if($item->keterangan)
                                            <span class="text-xs text-muted max-w-[200px] truncate" title="{{ $item->keterangan }}">{{ $item->keterangan }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Notes --}}
        <div class="glass-card p-4">
            <h3 class="text-sm font-semibold text-primary mb-2">Catatan</h3>
            @if($editing)
                <textarea wire:model="editNotes" rows="3"
                    class="w-full px-3 py-2 rounded-lg text-sm resize-none transition-colors duration-200"
                    style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                    placeholder="Tambahkan catatan..."></textarea>
            @else
                <p class="text-sm text-secondary whitespace-pre-wrap">{{ $form->notes ?? '-' }}</p>
            @endif
        </div>

        {{-- Previous Approvals --}}
        @if($form->approvals->count() > 0)
            <div class="glass-card p-4">
                <h3 class="text-sm font-semibold text-primary mb-3">Riwayat Approval</h3>
                <div class="space-y-3">
                    @foreach($form->approvals->sortBy('created_at') as $approval)
                        <div class="flex items-center gap-3 p-3 rounded-lg" style="background: var(--color-bg-secondary);">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center
                                {{ $approval->status === 'approved' ? 'bg-emerald-500/20 text-emerald-400' : ($approval->status === 'rejected' ? 'bg-red-500/20 text-red-400' : 'bg-yellow-500/20 text-yellow-400') }}">
                                @if($approval->status === 'approved')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @elseif($approval->status === 'rejected')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-primary">
                                    {{ $approval->approval_level === 'diperiksa_oleh' ? 'Diperiksa Oleh' : ($approval->approval_level === 'diketahui_oleh' ? 'Diketahui Oleh' : 'Disetujui Oleh') }}
                                </div>
                                <div class="text-xs text-muted">
                                    {{ $approval->user->name }} &middot; {{ $approval->approved_at ? $approval->approved_at->format('d M Y H:i') : '-' }}
                                    &middot; <span class="{{ $approval->status === 'approved' ? 'text-emerald-400' : ($approval->status === 'rejected' ? 'text-red-400' : 'text-yellow-400') }}">{{ ucfirst($approval->status) }}</span>
                                </div>
                                @if($approval->catatan)
                                    <div class="text-xs text-secondary mt-1">{{ $approval->catatan }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- APPROVE / REJECT --}}
        @if($canApprove)
            <div class="glass-card p-5 space-y-4 border-2" style="border-color: var(--color-primary, rgba(59, 130, 246, 0.3));">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-base font-bold text-primary">Edit, Submit & Tanda Tangan</h3>
                </div>
                <p class="text-xs text-muted -mt-2">Edit data jika diperlukan, lalu tanda tangani untuk approve.</p>

                <h3 class="text-sm font-semibold text-primary">Catatan Approval (opsional)</h3>
                <textarea wire:model.live="catatan" rows="2"
                    class="glass-input w-full rounded-lg px-3 py-2 text-sm resize-none"
                    placeholder="Tambahkan catatan approval..."></textarea>

                {{-- Signature Pad --}}
                <h3 class="text-sm font-semibold text-primary">Tanda Tangan</h3>
                <div x-data="{
                    canvas: null, ctx: null, drawing: false, lastX: 0, lastY: 0,
                    init() { this.canvas = this.$refs.signatureCanvas; this.ctx = this.canvas.getContext('2d'); this.resize(); window.addEventListener('resize', () => this.resize()); },
                    resize() { const rect = this.canvas.parentElement.getBoundingClientRect(); this.canvas.width = rect.width; this.canvas.height = 200; this.ctx.strokeStyle = getComputedStyle(document.documentElement).getPropertyValue('--color-text-primary').trim(); this.ctx.lineWidth = 2; this.ctx.lineCap = 'round'; this.ctx.lineJoin = 'round'; },
                    startDraw(e) { this.drawing = true; const rect = this.canvas.getBoundingClientRect(); const touch = e.touches ? e.touches[0] : e; this.lastX = touch.clientX - rect.left; this.lastY = touch.clientY - rect.top; },
                    draw(e) { if (!this.drawing) return; const rect = this.canvas.getBoundingClientRect(); const touch = e.touches ? e.touches[0] : e; const x = touch.clientX - rect.left; const y = touch.clientY - rect.top; this.ctx.beginPath(); this.ctx.moveTo(this.lastX, this.lastY); this.ctx.lineTo(x, y); this.ctx.stroke(); this.lastX = x; this.lastY = y; },
                    stopDraw() { this.drawing = false; },
                    clear() { this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height); },
                    isEmpty() { const pixel = this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height).data; return !pixel.some(v => v !== 0); },
                    save() { if (this.isEmpty()) { alert('Harap tanda tangan terlebih dahulu'); return; } $wire.approveForm(this.canvas.toDataURL('image/png')); }
                }" class="space-y-3">
                    <div class="rounded-lg overflow-hidden border-2" style="border-color: var(--color-border);">
                        <canvas x-ref="signatureCanvas" class="w-full cursor-crosshair touch-none"
                            style="background: var(--color-bg-secondary); height: 200px;"
                            @mousedown="startDraw($event)" @mousemove="draw($event)" @mouseup="stopDraw()" @mouseleave="stopDraw()"
                            @touchstart.prevent="startDraw($event)" @touchmove.prevent="draw($event)" @touchend="stopDraw()">
                        </canvas>
                    </div>
                    <div class="flex gap-2">
                        <button @click="clear()" type="button" class="glass-button-secondary text-sm flex-1">Hapus</button>
                        <button @click="save()" type="button" class="glass-button-primary text-sm flex-1">Submit & Tanda Tangan</button>
                    </div>
                </div>

                {{-- Reject --}}
                <div class="pt-2 border-t" style="border-color: var(--color-border);">
                    <button wire:click="toggleRejectModal" type="button"
                        class="w-full px-4 py-2 rounded-lg font-medium text-sm bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-all duration-200 border border-red-500/20">
                        Reject & Kembalikan ke Teknisi
                    </button>
                </div>
            </div>
        @endif

        {{-- REJECT MODAL --}}
        @if($showRejectModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);"
                x-data x-on:keydown.escape.window="$wire.set('showRejectModal', false)">
                <div class="glass-card p-6 w-full max-w-md space-y-4" @click.away="$wire.set('showRejectModal', false)">
                    <h3 class="text-lg font-bold text-primary">Reject Form</h3>
                    <p class="text-sm text-muted">Form akan dikembalikan ke teknisi dengan status "Revisi".</p>
                    <div>
                        <label class="text-xs text-muted">Alasan Reject <span class="text-red-400">*</span></label>
                        <textarea wire:model.live="rejectReason" rows="3"
                            class="glass-input w-full rounded-lg px-3 py-2 text-sm resize-none mt-1"
                            placeholder="Jelaskan alasan reject..."></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="toggleRejectModal" type="button" class="glass-button-secondary text-sm flex-1">Batal</button>
                        <button wire:click="rejectForm" type="button" class="flex-1 px-4 py-2 rounded-lg font-medium text-sm bg-red-500 text-white hover:bg-red-600 transition-all duration-200">Reject</button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
