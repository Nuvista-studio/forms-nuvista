<div class="space-y-6">
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
                    <p class="text-xs text-muted">Format: no_asset, kategori, brand, tipe, nama_perangkat, no_serial, operating_unit, site_location_asset, assigned_user_email</p>
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

            @error('file') <p class="text-xs text-red-400">{{ $message }}</p> @enderror
        </div>

        @if(!empty($preview) && empty($errors))
            <div class="glass-card p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-primary">Preview ({{ min($totalRows, 10) }} dari {{ $totalRows }} baris)</h3>
                    <button wire:click="import" wire:loading.attr="disabled"
                        class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
                        style="background: var(--color-primary); color: var(--color-button-text);">
                        <span wire:loading.remove wire:target="import">Import {{ $totalRows }} Data</span>
                        <span wire:loading wire:target="import">Mengimport...</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b" style="border-color: var(--color-border);">
                                <th class="px-3 py-2 text-left text-muted font-medium">#</th>
                                <th class="px-3 py-2 text-left text-muted font-medium">No Asset</th>
                                <th class="px-3 py-2 text-left text-muted font-medium">Kategori</th>
                                <th class="px-3 py-2 text-left text-muted font-medium">Brand</th>
                                <th class="px-3 py-2 text-left text-muted font-medium hidden sm:table-cell">Tipe</th>
                                <th class="px-3 py-2 text-left text-muted font-medium hidden md:table-cell">Nama Perangkat</th>
                                <th class="px-3 py-2 text-left text-muted font-medium hidden lg:table-cell">User</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color: var(--color-border);">
                            @foreach($preview as $i => $row)
                                <tr>
                                    <td class="px-3 py-2 text-muted">{{ $i + 1 }}</td>
                                    <td class="px-3 py-2 text-primary font-mono font-medium">{{ $row['no_asset'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-secondary">{{ $row['kategori'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-secondary">{{ $row['brand'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-secondary hidden sm:table-cell">{{ $row['tipe'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-secondary hidden md:table-cell">{{ $row['nama_perangkat'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-secondary hidden lg:table-cell">{{ $row['assigned_user_email'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if(!empty($errors))
            <div class="glass-card p-6 space-y-3">
                <h3 class="font-semibold text-red-400">Error</h3>
                <div class="space-y-1 max-h-60 overflow-y-auto">
                    @foreach($errors as $error)
                        <p class="text-xs text-red-400">{{ $error }}</p>
                    @endforeach
                </div>
                <button wire:click="resetImport"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200"
                    style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                    Coba Lagi
                </button>
            </div>
        @endif
    @endif

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

            @if(!empty($errors))
                <div class="text-left max-h-40 overflow-y-auto mt-4 p-3 rounded-lg" style="background: var(--color-glass-bg); border: 1px solid var(--color-border);">
                    @foreach($errors as $error)
                        <p class="text-xs text-red-400">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="flex items-center justify-center gap-3 pt-2">
                <a href="{{ route('admin.assets.index') }}" wire:navigate
                    class="px-5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200"
                    style="background: var(--color-primary); color: var(--color-button-text);">
                    Lihat Assets
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
