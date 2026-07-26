<div class="space-y-5"
    x-data="{ errors: {} }"
    x-on:validation-error.window="errors = $event.detail.errors[0]"
    x-on:template-updated.window="errors = {}; window.location = '{{ route('admin.pdf-templates.index') }}'">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-primary">Edit Template: {{ $name }}</h1>
            <p class="text-sm text-muted mt-1 font-mono">Slug: {{ $slug }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pdf-templates.preview', $slug) }}" target="_blank"
                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-200"
                style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Preview
            </a>
        </div>
    </div>

    {{-- Name & Status --}}
    <div class="glass-card p-5 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-secondary mb-1">Nama Template <span class="text-red-400">*</span></label>
                <input wire:model="name" type="text"
                    class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
                    style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                    placeholder="Contoh: Form Pemeriksaan" />
                @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-end">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="isActive" type="checkbox" class="w-4 h-4 rounded" />
                    <span class="text-sm text-secondary">Template Aktif</span>
                </label>
            </div>
        </div>
    </div>

    {{-- HTML Editor --}}
    <div class="glass-card p-5 space-y-3">
        <div class="flex items-center justify-between">
            <label class="block text-sm font-medium text-secondary">HTML Content <span class="text-red-400">*</span></label>
            <button wire:click="resetToDefault" type="button"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200 text-amber-400 hover:text-amber-300"
                style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3);"
                onclick="return confirm('Reset template ke default? Semua perubahan akan hilang.')">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Reset ke Default
            </button>
        </div>
        <textarea wire:model="htmlContent" rows="30"
            class="w-full px-4 py-3 rounded-lg text-xs font-mono leading-relaxed transition-colors duration-200 resize-y"
            style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary); min-height: 600px; tab-size: 4;"
            placeholder="Masukkan HTML template di sini..."></textarea>
        @error('htmlContent') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        @php
            echo '<p class="text-xs text-muted">Gunakan syntax Blade: <code>{{ \'{{ $variable }}\' }}</code> atau <code>{!! \'{!! $variable !!}\' !!}</code></p>';
        @endphp
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3">
        <button wire:click="save" wire:loading.attr="disabled"
            class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
            style="background: var(--color-primary); color: var(--color-button-text);">
            <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </button>
        <a href="{{ route('admin.pdf-templates.index') }}" wire:navigate
            class="px-5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200"
            style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
            Batal
        </a>
    </div>
</div>
