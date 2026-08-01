<div class="space-y-6">
    {{-- Toast Notification --}}
    <div x-data="{ toast: false, message: '', type: 'success' }"
        @show-toast.window="toast = true; message = $event.detail.message; type = $event.detail.type || 'success'; setTimeout(() => toast = false, 4000)"
        x-on:livewire-upload-error.window="toast = true; message = 'Gagal mengunggah file CSV. Periksa ukuran (maks 10MB) dan format file, lalu coba lagi.'; type = 'error'; setTimeout(() => toast = false, 4000)"
        x-show="toast" x-transition
        class="fixed top-20 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium max-w-xs"
        :class="type === 'success' ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'"
        x-text="message">
    </div>

    {{-- Upload Section --}}
    @if(!$imported)
        <div class="glass-card p-6 space-y-4">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: var(--color-glass-bg); border: 1px solid var(--color-border);">
                    <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-primary">Upload File CSV</h3>
                    <p class="text-xs text-muted">Format: name, email, password, nik, department, business_unit, site, no_telepon, role</p>
                </div>
            </div>

            <div class="relative border-2 border-dashed rounded-lg p-8 text-center transition-colors duration-200"
                 style="border-color: var(--color-border);"
                 x-data="{ dragging: false }"
                 x-on:dragover.prevent="dragging = true"
                 x-on:dragleave="dragging = false"
                 x-on:drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                 :class="{ 'border-blue-400 bg-blue-500/5': dragging }">

                <svg class="w-10 h-10 mx-auto text-muted mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm text-secondary mb-2">Seret file ke sini atau klik untuk memilih</p>
                <p class="text-xs text-muted">Maksimal 10MB (.csv)</p>

                <input type="file" wire:model="file" x-ref="fileInput" accept=".csv,.txt"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
            </div>

            <div x-data="{ progress: 0, uploading: false, timedOut: false, timer: null }"
                x-on:livewire-upload-start.window="uploading = true; progress = 0; timedOut = false; clearTimeout(timer)"
                x-on:livewire-upload-progress.window="progress = $event.detail.progress"
                x-on:livewire-upload-finish.window="clearTimeout(timer); uploading = false"
                x-on:livewire-upload-error.window="clearTimeout(timer); uploading = false"
                x-effect="if (progress >= 100 && !timer && uploading) { timer = setTimeout(() => { uploading = false; timedOut = true; }, 20000); }"
                x-show="uploading || timedOut"
                class="flex flex-col items-center gap-2 text-xs"
                style="color: var(--color-primary);">
                <div class="flex items-center gap-2">
                    <svg x-show="!timedOut" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span x-show="!timedOut" x-text="progress >= 100 ? 'Memproses file, mohon tunggu...' : 'Mengunggah file, mohon tunggu... ' + progress + '%'"></span>
                    <span x-show="timedOut" style="color: var(--color-text-secondary);">Pemrosesan memakan waktu terlalu lama.</span>
                </div>
                <div class="h-1.5 w-full max-w-xs rounded-full overflow-hidden" style="background: var(--color-glass-bg);">
                    <div class="h-full rounded-full transition-all duration-150" style="background: var(--color-primary);" :style="'width: ' + progress + '%'"></div>
                </div>
                <template x-if="timedOut">
                    <p class="text-xs" style="color: var(--color-text-secondary);">Muat ulang halaman lalu pilih file CSV lagi.</p>
                </template>
            </div>

            @error('file') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        {{-- Preview Section --}}
        @if(!empty($preview) && empty($importErrors))
            <div class="glass-card p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-primary">Preview ({{ min($totalRows, 5) }} dari {{ $totalRows }} baris)</h3>
                    <button wire:click="import" wire:loading.attr="disabled"
                        class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
                        style="background: var(--color-primary); color: var(--color-button-text);">
                        <span wire:loading.remove wire:target="import">Import {{ $totalRows }} Data</span>
                        <span wire:loading wire:target="import">Mengimport...</span>
                    </button>
                </div>

                <div wire:loading wire:target="import" class="space-y-2">
                    <div class="flex items-center gap-2 text-xs" style="color: var(--color-primary);">
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Mengimport data...</span>
                        <span class="font-semibold" wire:stream="importProgressPercent">0%</span>
                    </div>
                    <div class="h-2 w-full rounded-full overflow-hidden" style="background: var(--color-glass-bg);">
                        <div class="h-full rounded-full transition-all duration-300" style="background: var(--color-primary); width: 0%;" wire:stream="importProgressBar"></div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b" style="border-color: var(--color-border);">
                                <th class="px-3 py-2 text-left text-muted font-medium whitespace-nowrap">#</th>
                                <th class="px-3 py-2 text-left text-muted font-medium whitespace-nowrap">Nama</th>
                                <th class="px-3 py-2 text-left text-muted font-medium whitespace-nowrap">Email</th>
                                <th class="px-3 py-2 text-left text-muted font-medium whitespace-nowrap">Password</th>
                                <th class="px-3 py-2 text-left text-muted font-medium whitespace-nowrap">NIK</th>
                                <th class="px-3 py-2 text-left text-muted font-medium whitespace-nowrap">Department</th>
                                <th class="px-3 py-2 text-left text-muted font-medium whitespace-nowrap">Corp Unit</th>
                                <th class="px-3 py-2 text-left text-muted font-medium whitespace-nowrap">Site</th>
                                <th class="px-3 py-2 text-left text-muted font-medium whitespace-nowrap">No Telepon</th>
                                <th class="px-3 py-2 text-left text-muted font-medium whitespace-nowrap">Role</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color: var(--color-border);">
                            @foreach($preview as $i => $row)
                                <tr>
                                    <td class="px-3 py-2 text-muted whitespace-nowrap">{{ $i + 1 }}</td>
                                    <td class="px-3 py-2 text-primary font-medium whitespace-nowrap">{{ $row['name'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-secondary whitespace-nowrap">{{ $row['email'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-secondary font-mono whitespace-nowrap">{{ $row['password'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-secondary font-mono whitespace-nowrap">{{ $row['nik'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-secondary whitespace-nowrap">{{ $row['department'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-secondary whitespace-nowrap">{{ $row['business_unit'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-secondary whitespace-nowrap">{{ $row['site'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-secondary whitespace-nowrap">{{ $row['no_telepon'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-secondary whitespace-nowrap">{{ $row['role'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Errors --}}
        @if(!empty($importErrors))
            <div class="glass-card p-6 space-y-3" style="border-color: rgba(248, 113, 113, 0.4);">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-red-400">Upload gagal / data tidak valid</h3>
                    <span class="text-xs font-semibold text-red-400 bg-red-500/10 px-2 py-1 rounded-full">{{ count($importErrors) }} masalah</span>
                </div>
                <div class="space-y-1 max-h-60 overflow-y-auto">
                    @foreach($importErrors as $error)
                        <p class="text-xs text-red-400">• {{ $error }}</p>
                    @endforeach
                </div>
                <button wire:click="resetImport"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200"
                    style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                    Pilih File Lain / Coba Lagi
                </button>
            </div>
        @endif
    @endif

    {{-- Import Result --}}
    @if($imported)
        <div class="glass-card p-8 text-center space-y-4">
            <div class="w-16 h-16 mx-auto rounded-full flex items-center justify-center" style="background: rgba(52, 211, 153, 0.15);">
                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-primary">Import Selesai</h3>
            <div class="flex items-center justify-center gap-6 text-sm">
                <div>
                    <span class="text-emerald-400 font-bold text-xl">{{ $successCount }}</span>
                    <p class="text-muted text-xs">Berhasil</p>
                </div>
                <div>
                    <span class="text-red-400 font-bold text-xl">{{ $errorCount }}</span>
                    <p class="text-muted text-xs">Gagal</p>
                </div>
            </div>

            @if(!empty($importErrors))
                <div class="text-left mt-4 p-3 rounded-lg" style="background: var(--color-glass-bg); border: 1px solid var(--color-border);">
                    <p class="text-xs font-semibold text-red-400 mb-2">Detail Error ({{ $errorCount }} baris gagal):</p>
                    @php
                        $errorGroups = [];
                        foreach ($importErrors as $error) {
                            $key = preg_replace('/^Baris \d+: /', '', trim($error));
                            $errorGroups[$key] = ($errorGroups[$key] ?? 0) + 1;
                        }
                    @endphp
                    <div class="space-y-1 mb-2">
                        @foreach($errorGroups as $message => $count)
                            <p class="text-xs text-red-400">• {{ $message }} <span class="text-muted">({{ $count }}×)</span></p>
                        @endforeach
                    </div>
                    <details>
                        <summary class="text-xs text-secondary cursor-pointer hover:text-primary">Lihat detail per baris</summary>
                        <div class="max-h-40 overflow-y-auto mt-2 space-y-1">
                            @foreach($importErrors as $error)
                                <p class="text-xs text-red-400">• {{ $error }}</p>
                            @endforeach
                        </div>
                    </details>
                </div>
            @endif

            @if($errorCount > 0 && $successCount === 0)
                <p class="text-sm font-medium text-red-400">Seluruh data gagal diimport. Tidak ada data yang ditambahkan.</p>
            @endif

            <div class="flex items-center justify-center gap-3 pt-2">
                <a href="{{ route('admin.users.index') }}" wire:navigate
                    class="px-5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200"
                    style="background: var(--color-primary); color: var(--color-button-text);">
                    Lihat Users
                </a>
                <button wire:click="resetImport"
                    class="px-5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200"
                    style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                    Import Lagi
                </button>
            </div>
        </div>
    @endif
</div>
