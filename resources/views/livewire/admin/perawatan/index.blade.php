<div class="space-y-6" x-data="columnManager()" x-init="init(['status','nomor_form','teknisi','pengguna','perangkat','site','kondisi_akhir','tanggal','aksi'])" x-on:form-deleted.window="$wire.$refresh()">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-primary">Form Perawatan (PWT)</h1>
            <p class="text-sm text-muted mt-1">Daftar seluruh formulir perawatan perangkat</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-sm text-muted">{{ $forms->total() }} form</span>
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
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200"
                    style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                    class="absolute right-0 mt-1 w-44 rounded-lg shadow-lg z-30 py-1"
                    style="background: var(--color-card-bg); border: 1px solid var(--color-card-border);"
                    x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                    <a href="{{ route('admin.perawatan.export', ['format' => 'pdf']) }}" class="block px-4 py-2 text-xs text-primary hover:bg-[var(--color-bg-tertiary)]">Export as PDF</a>
                    <a href="{{ route('admin.perawatan.export', ['format' => 'xlsx']) }}" class="block px-4 py-2 text-xs text-primary hover:bg-[var(--color-bg-tertiary)]">Export as XLSX</a>
                    <a href="{{ route('admin.perawatan.export', ['format' => 'xls']) }}" class="block px-4 py-2 text-xs text-primary hover:bg-[var(--color-bg-tertiary)]">Export as XLS</a>
                    <a href="{{ route('admin.perawatan.export', ['format' => 'html']) }}" class="block px-4 py-2 text-xs text-primary hover:bg-[var(--color-bg-tertiary)]">Export as HTML</a>
                    <a href="{{ route('admin.perawatan.export', ['format' => 'csv']) }}" class="block px-4 py-2 text-xs text-primary hover:bg-[var(--color-bg-tertiary)]">Export as CSV</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="glass-card p-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="sm:col-span-1">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Cari no form, teknisi, pengguna, perangkat..."
                        class="w-full pl-10 pr-4 py-2 rounded-lg text-sm transition-colors duration-200"
                        style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"/>
                </div>
            </div>
            <div>
                <select wire:model.live="status" class="w-full px-3 py-2 rounded-lg text-sm transition-colors duration-200"
                    style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="diketahui">Diketahui</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="selesai">Selesai</option>
                    <option value="revisi">Revisi</option>
                </select>
            </div>
            <div>
                <select wire:model.live="kondisi_akhir" class="w-full px-3 py-2 rounded-lg text-sm transition-colors duration-200"
                    style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);">
                    <option value="">Semua Kondisi Akhir</option>
                    <option value="good_normal">Good / Normal</option>
                    <option value="caution_poor">Caution / Poor</option>
                    <option value="good">Good</option>
                    <option value="fair">Fair</option>
                    <option value="critical">Critical</option>
                    <option value="poor">Poor</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    @if($forms->count() > 0)
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b" style="border-color: var(--color-border);">
                            <th x-show="isVisible('status')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Status</th>
                            <th x-show="isVisible('nomor_form')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">No. Form</th>
                            <th x-show="isVisible('teknisi')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden sm:table-cell">Teknisi</th>
                            <th x-show="isVisible('pengguna')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden md:table-cell">Pengguna</th>
                            <th x-show="isVisible('perangkat')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden lg:table-cell">Perangkat</th>
                            <th x-show="isVisible('site')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden lg:table-cell">Site</th>
                            <th x-show="isVisible('kondisi_akhir')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">Kondisi Akhir</th>
                            <th x-show="isVisible('tanggal')" class="px-4 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider hidden xl:table-cell">Tanggal</th>
                            <th x-show="isVisible('aksi')" class="px-4 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--color-border);">
                        @foreach($forms as $form)
                            <tr class="transition-colors duration-150" style="hover: background: var(--color-glass-bg);">
                                <td x-show="isVisible('status')" class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'draft' => 'background: rgba(107,114,128,0.15); color: #6b7280;',
                                            'submitted' => 'background: rgba(59,130,246,0.15); color: #3b82f6;',
                                            'diketahui' => 'background: rgba(234,179,8,0.15); color: #eab308;',
                                            'disetujui' => 'background: rgba(34,197,94,0.15); color: #22c55e;',
                                            'selesai' => 'background: rgba(16,185,129,0.15); color: #10b981;',
                                            'revisi' => 'background: rgba(239,68,68,0.15); color: #ef4444;',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold" style="{{ $statusColors[$form->status] ?? '' }}">
                                        {{ ucfirst($form->status) }}
                                    </span>
                                </td>
                                <td x-show="isVisible('nomor_form')" class="px-4 py-3 font-mono text-secondary text-xs">{{ $form->nomor_form }}</td>
                                <td x-show="isVisible('teknisi')" class="px-4 py-3 text-primary hidden sm:table-cell">{{ $form->teknisi->name ?? '-' }}</td>
                                <td x-show="isVisible('pengguna')" class="px-4 py-3 text-primary hidden md:table-cell">{{ $form->pengguna->name ?? '-' }}</td>
                                <td x-show="isVisible('perangkat')" class="px-4 py-3 hidden lg:table-cell">
                                    <div class="font-medium text-primary text-xs">{{ $form->asset->nama_perangkat ?? '-' }}</div>
                                    @if($form->asset)
                                        <div class="text-xs text-muted mt-0.5 font-mono">{{ $form->asset->no_asset }}</div>
                                    @endif
                                </td>
                                <td x-show="isVisible('site')" class="px-4 py-3 text-secondary text-xs hidden lg:table-cell">{{ $form->site->site ?? $form->site_location ?? '-' }}</td>
                                <td x-show="isVisible('kondisi_akhir')" class="px-4 py-3">
                                    @php
                                        $kondisiColors = [
                                            'good' => 'background: rgba(16,185,129,0.15); color: #10b981;',
                                            'good_normal' => 'background: rgba(16,185,129,0.15); color: #10b981;',
                                            'fair' => 'background: rgba(59,130,246,0.15); color: #3b82f6;',
                                            'critical' => 'background: rgba(245,158,11,0.15); color: #f59e0b;',
                                            'poor' => 'background: rgba(239,68,68,0.15); color: #ef4444;',
                                            'caution_poor' => 'background: rgba(245,158,11,0.15); color: #f59e0b;',
                                        ];
                                        $kondisiLabels = [
                                            'good_normal' => 'Good/Normal',
                                            'caution_poor' => 'Caution/Poor',
                                        ];
                                    @endphp
                                    @if($form->kondisi_akhir)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold" style="{{ $kondisiColors[$form->kondisi_akhir] ?? '' }}">
                                            {{ $kondisiLabels[$form->kondisi_akhir] ?? ucfirst($form->kondisi_akhir) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-muted">-</span>
                                    @endif
                                </td>
                                <td x-show="isVisible('tanggal')" class="px-4 py-3 text-muted text-xs hidden xl:table-cell">{{ $form->submitted_at?->format('d/m/Y') ?? '-' }}</td>
                                <td x-show="isVisible('aksi')" class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button wire:click="viewForm({{ $form->id }})"
                                            class="p-1.5 rounded-lg transition-colors duration-200"
                                            style="color: var(--color-text-secondary);" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <a href="{{ route('perawatan.export-pdf', $form->id) }}" target="_blank"
                                            class="p-1.5 rounded-lg transition-colors duration-200" style="color: var(--color-text-secondary);" title="Export PDF">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $forms->links() }}
        </div>
    @else
        <div class="glass-card p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="mt-3 text-muted">Tidak ada form perawatan ditemukan</p>
        </div>
    @endif

    {{-- Detail Modal --}}
    @if($viewingForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);"
            x-data x-on:keydown.escape.window="$wire.closeView()">
            <div class="glass-card w-full max-w-3xl max-h-[85vh] overflow-y-auto p-6 space-y-5"
                @click.away="$wire.closeView()">

                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-primary">Detail Form Perawatan</h2>
                        <p class="text-xs text-muted font-mono mt-0.5">{{ $viewingForm['nomor_form'] }}</p>
                    </div>
                    <button wire:click="closeView" class="text-muted hover:text-primary text-xl">&times;</button>
                </div>

                {{-- Info Section --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                    <div><span class="text-muted text-xs">Status</span>
                        @php
                            $sc = match($viewingForm['status']) {
                                'draft' => 'color: #6b7280;',
                                'submitted' => 'color: #3b82f6;',
                                'diketahui' => 'color: #eab308;',
                                'disetujui' => 'color: #22c55e;',
                                'selesai' => 'color: #10b981;',
                                'revisi' => 'color: #ef4444;',
                                default => '',
                            };
                        @endphp
                        <p class="font-semibold" style="{{ $sc }}">{{ ucfirst($viewingForm['status']) }}</p>
                    </div>
                    <div><span class="text-muted text-xs">Tanggal</span><p class="text-primary">{{ $viewingForm['submitted_at'] ?? '-' }}</p></div>
                    <div><span class="text-muted text-xs">Kondisi Akhir</span><p class="text-primary">{{ ucfirst(str_replace('_', ' ', $viewingForm['kondisi_akhir'] ?? '-')) }}</p></div>
                    <div><span class="text-muted text-xs">Teknisi</span><p class="text-primary">{{ $viewingForm['teknisi']['name'] ?? '-' }}</p></div>
                    <div><span class="text-muted text-xs">Pengguna</span><p class="text-primary">{{ $viewingForm['pengguna']['name'] ?? '-' }}</p></div>
                    <div><span class="text-muted text-xs">NIK</span><p class="text-primary">{{ $viewingForm['pengguna']['nik'] ?? '-' }}</p></div>
                    <div><span class="text-muted text-xs">Perangkat</span><p class="text-primary">{{ $viewingForm['asset']['nama_perangkat'] ?? '-' }}</p></div>
                    <div><span class="text-muted text-xs">No. Asset</span><p class="text-primary font-mono text-xs">{{ $viewingForm['asset']['no_asset'] ?? '-' }}</p></div>
                    <div><span class="text-muted text-xs">Site</span><p class="text-primary">{{ $viewingForm['site']['site'] ?? $viewingForm['site_location'] ?? '-' }}</p></div>
                    @if($viewingForm['kondisi_akhir_notes'])
                        <div class="col-span-2 sm:col-span-3"><span class="text-muted text-xs">Keterangan Kondisi Akhir</span><p class="text-primary">{{ $viewingForm['kondisi_akhir_notes'] }}</p></div>
                    @endif
                    @if($viewingForm['location_detail'])
                        <div class="col-span-2 sm:col-span-3"><span class="text-muted text-xs">Location Detail</span><p class="text-primary">{{ $viewingForm['location_detail'] }}</p></div>
                    @endif
                </div>

                {{-- Items --}}
                @if(count($viewingForm['items']) > 0)
                    @php
                        $hwItems = collect($viewingForm['items'])->where('category', 'hardware');
                        $appItems = collect($viewingForm['items'])->where('category', 'aplikasi');
                        $osItems = collect($viewingForm['items'])->where('category', 'operating_system');
                    @endphp
                    <div class="space-y-3">
                        @foreach(['Hardware' => $hwItems, 'Aplikasi' => $appItems, 'Operating System' => $osItems] as $catName => $items)
                            @if($items->count() > 0)
                                <div>
                                    <h3 class="text-xs font-semibold text-muted uppercase mb-2">{{ $catName }}</h3>
                                    <div class="space-y-1">
                                        @foreach($items as $item)
                                            <div class="flex items-center justify-between py-1.5 px-3 rounded text-xs" style="background: var(--color-bg-tertiary);">
                                                <span class="text-primary">{{ $item['name'] }}</span>
                                                <div class="flex items-center gap-3">
                                                    @if($item['keterangan'])
                                                        <span class="text-muted max-w-[200px] truncate" title="{{ $item['keterangan'] }}">{{ $item['keterangan'] }}</span>
                                                    @endif
                                                    <span class="{{ $item['status'] === 'baik' ? 'text-emerald-400' : ($item['status'] === 'tidak_baik' ? 'text-red-400' : 'text-muted') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $item['status'] ?? '-')) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- Notes + Barcode --}}
                @if($viewingForm['notes'] || $viewingForm['barcode_fisik'])
                    <div>
                        <h3 class="text-xs font-semibold text-muted uppercase mb-1">Catatan Tambahan</h3>
                        <div class="px-3 py-2 rounded space-y-1" style="background: var(--color-bg-tertiary);">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-muted">Barcode Fisik:</span>
                                @if($viewingForm['barcode_fisik'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold" style="background: rgba(16,185,129,0.15); color: #10b981;">Ada</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold" style="background: rgba(239,68,68,0.15); color: #ef4444;">Tidak Ada</span>
                                @endif
                            </div>
                            @if($viewingForm['notes'])
                                <p class="text-sm text-primary">{{ $viewingForm['notes'] }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Approvals --}}
                @if(count($viewingForm['approvals']) > 0)
                    <div>
                        <h3 class="text-xs font-semibold text-muted uppercase mb-2">Approval History</h3>
                        <div class="space-y-1">
                            @foreach($viewingForm['approvals'] as $approval)
                                <div class="flex items-center justify-between py-1.5 px-3 rounded text-xs" style="background: var(--color-bg-tertiary);">
                                    <span class="text-primary">{{ ucfirst(str_replace('_', ' ', $approval['approval_level'])) }} &mdash; {{ $approval['user_name'] ?? '-' }}</span>
                                    <span class="text-muted">{{ ucfirst($approval['status']) }} {{ $approval['approved_at'] ?? '' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="flex justify-end gap-2 pt-2 border-t" style="border-color: var(--color-border);">
                    <a href="{{ route('perawatan.export-pdf', $viewingForm['id']) }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-medium transition-colors duration-200"
                        style="background: var(--color-primary); color: var(--color-button-text);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export PDF
                    </a>
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
            const stored = localStorage.getItem('perawatan_columns');
            if (stored) {
                try { this.columns = JSON.parse(stored); } catch(e) { this.columns = this.defaultColumns(defaults); }
            } else {
                this.columns = this.defaultColumns(defaults);
            }
        },
        defaultColumns(keys) {
            const labels = {
                status: 'Status', nomor_form: 'No. Form', teknisi: 'Teknisi',
                pengguna: 'Pengguna', perangkat: 'Perangkat', site: 'Site',
                kondisi_akhir: 'Kondisi Akhir', tanggal: 'Tanggal', aksi: 'Aksi'
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
                const newOrder = items.map(label => {
                    const spans = label.querySelectorAll('span:not([x-text])');
                    const idxSpan = label.querySelector('span:last-child');
                    return parseInt(idxSpan?.textContent || '1') - 1;
                });
                const sorted = newOrder.map(i => this.columns[i]).filter(Boolean);
                if (sorted.length === this.columns.length) this.columns = sorted;
                this.save();
            };
            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
        },
        save() { localStorage.setItem('perawatan_columns', JSON.stringify(this.columns)); },
        resetColumns() {
            localStorage.removeItem('perawatan_columns');
            this.columns = this.defaultColumns(['status','nomor_form','teknisi','pengguna','perangkat','site','kondisi_akhir','tanggal','aksi']);
        }
    };
}
</script>
@endpush
