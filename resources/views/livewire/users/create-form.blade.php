<div class="glass-card p-6 space-y-5"
    x-data="{ errors: {} }"
    x-on:validation-error.window="errors = $event.detail.errors[0]">

    @if($showCredentials)
        {{-- INFO AKSES LOGIN --}}
        <div class="rounded-lg p-5 space-y-3" style="background: var(--color-bg-tertiary); border: 1px solid var(--color-border);">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-semibold text-emerald-400">Pengguna berhasil ditambahkan!</span>
            </div>
            <p class="text-xs text-muted">Berikut informasi akses login yang dapat diberikan kepada pengguna:</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="rounded-lg p-3" style="background: var(--color-glass-bg); border: 1px solid var(--color-border);">
                    <span class="text-xs text-muted block mb-1">Email</span>
                    <p class="text-sm font-mono font-semibold text-primary">{{ $createdEmail }}</p>
                </div>
                <div class="rounded-lg p-3" style="background: var(--color-glass-bg); border: 1px solid var(--color-border);">
                    <span class="text-xs text-muted block mb-1">Password Default</span>
                    <p class="text-sm font-mono font-semibold text-primary">{{ $createdPassword }}</p>
                </div>
            </div>
            <p class="text-xs text-muted" style="color: var(--color-text-secondary);">
                <svg class="w-3.5 h-3.5 inline-block -mt-0.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Pengguna dapat mengubah password setelah login pertama kali.
            </p>
            <div class="pt-2">
                <a href="{{ route('admin.users.index') }}" wire:navigate
                    class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
                    style="background: var(--color-primary); color: var(--color-button-text);">
                    Selesai
                </a>
            </div>
        </div>
    @else
        {{-- FORM TAMBAH PENGGUNA --}}
        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-secondary mb-1">Nama <span class="text-red-400">*</span></label>
            <input wire:model="name" type="text"
                class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                placeholder="Nama lengkap" />
            @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-secondary mb-1">Email <span class="text-red-400">*</span></label>
            <input wire:model="email" type="email"
                class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                placeholder="email@asri.co.id" />
            @error('email') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Password --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-secondary mb-1">Password <span class="text-red-400">*</span></label>
                <input wire:model="password" type="password"
                    class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
                    style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                    placeholder="Minimal 6 karakter" />
                @error('password') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-secondary mb-1">Konfirmasi Password <span class="text-red-400">*</span></label>
                <input wire:model="password_confirmation" type="password"
                    class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
                    style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                    placeholder="Ulangi password" />
            </div>
        </div>

        {{-- NIK --}}
        <div>
            <label class="block text-sm font-medium text-secondary mb-1">NIK</label>
            <input wire:model="nik" type="text"
                class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                placeholder="Nomor Induk Karyawan" />
            @error('nik') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Department & Business Unit --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-secondary mb-1">Department</label>
                <input wire:model="department" type="text"
                    class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
                    style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                    placeholder="IT Operation" />
            </div>
            <div>
                <label class="block text-sm font-medium text-secondary mb-1">Business Unit</label>
                <input wire:model="business_unit" type="text"
                    class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
                    style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                    placeholder="ASRI" />
            </div>
        </div>

        {{-- Site & No. Telepon --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-secondary mb-1">Site</label>
                <input wire:model="site" type="text"
                    class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
                    style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                    placeholder="Head Office" />
            </div>
            <div>
                <label class="block text-sm font-medium text-secondary mb-1">No. Telepon</label>
                <input wire:model="no_telepon" type="text"
                    class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
                    style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                    placeholder="08xxxxxxxxxx" />
            </div>
        </div>

        {{-- Role --}}
        <div>
            <label class="block text-sm font-medium text-secondary mb-1">Role <span class="text-red-400">*</span></label>
            <select wire:model="role"
                class="w-full px-4 py-2 rounded-lg text-sm transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);">
                <option value="">Pilih Role</option>
                @foreach($this->getRoleList() as $r)
                    <option value="{{ $r }}">{{ $this->getRoleLabel($r) }}</option>
                @endforeach
            </select>
            @error('role') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 pt-2">
            <button wire:click="save" wire:loading.attr="disabled"
                class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200"
                style="background: var(--color-primary); color: var(--color-button-text);">
                <span wire:loading.remove wire:target="save">Simpan</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
            <a href="{{ route('admin.users.index') }}" wire:navigate
                class="px-5 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200"
                style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                Batal
            </a>
        </div>
    @endif
</div>
