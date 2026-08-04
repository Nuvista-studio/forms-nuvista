<div class="glass-card p-6 space-y-5"
    x-data="{ errors: {} }"
    x-on:validation-error.window="errors = $event.detail.errors[0]">

    <div x-data="{ toast: false, message: '', type: 'success' }"
        @show-toast.window="toast = true; message = $event.detail.message; type = $event.detail.type || 'success'; setTimeout(() => toast = false, 4000)"
        x-show="toast" x-transition
        class="fixed top-20 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-sm font-medium max-w-xs"
        :class="type === 'success' ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'"
        x-text="message">
    </div>

    {{-- Nama --}}
    <div>
        <label class="block text-sm font-medium text-secondary mb-1">{{ __('Nama') }} <span class="text-red-400">*</span></label>
        <input wire:model="name" type="text"
            class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
            style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
            placeholder="{{ __('Nama lengkap') }}" />
        @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Email (opsional) --}}
    <div>
        <label class="block text-sm font-medium text-secondary mb-1">{{ __('Email') }}</label>
        <input wire:model="email" type="email"
            class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
            style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
            placeholder="email@asri.co.id" />
        <p class="text-xs text-muted mt-1">{{ __('Opsional. Karyawan tanpa email tetap dapat diproses.') }}</p>
        @error('email') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- NIK --}}
    <div>
        <label class="block text-sm font-medium text-secondary mb-1">NIK</label>
        <input wire:model="nik" type="text"
            class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
            style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
            placeholder="{{ __('Nomor Induk Karyawan') }}" />
        @error('nik') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Site & No. Telepon --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-secondary mb-1">{{ __('Site') }}</label>
            <select wire:model="site"
                class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);">
                <option value="">{{ __('Pilih Site') }}</option>
                @foreach($this->getSiteList() as $idSite => $label)
                    <option value="{{ $idSite }}">{{ $label }}</option>
                @endforeach
            </select>
            @error('site') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-secondary mb-1">{{ __('No. Telepon') }}</label>
            <input wire:model="no_telepon" type="text"
                class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                placeholder="08xxxxxxxxxx" />
        </div>
    </div>

    {{-- Status --}}
    <div>
        <label class="block text-sm font-medium text-secondary mb-1">{{ __('Status Employee') }}</label>
        <select wire:model="status"
            class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
            style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);">
            <option value="Active">Active</option>
            <option value="Resigned">Resigned</option>
        </select>
        @error('status') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3 pt-2">
        <button wire:click="save" wire:loading.attr="disabled"
            class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
            style="background: var(--color-primary); color: var(--color-button-text);">
            <span wire:loading.remove wire:target="save">{{ __('Simpan') }}</span>
            <span wire:loading wire:target="save">{{ __('Menyimpan') }}...</span>
        </button>
        <a href="{{ route('admin.employees.index') }}" wire:navigate
            class="px-5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200"
            style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
            {{ __('Batal') }}
        </a>
    </div>
</div>
