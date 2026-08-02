<div class="space-y-6" x-data x-on:user-updated.window="window.location = '{{ route('admin.users.index') }}'">
    @if (session()->has('success'))
        <div class="p-3 rounded-lg text-sm"
            style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #22c55e;">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="update" class="space-y-5">
        {{-- Name --}}
        <div>
            <label class="text-xs text-muted">{{ __('Nama') }} <span class="text-red-400">*</span></label>
            <input wire:model="name" type="text"
                class="w-full px-3 py-2 rounded-lg text-sm mt-1 transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                required />
            @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="text-xs text-muted">{{ __('Email') }} <span class="text-red-400">*</span></label>
            <input wire:model="email" type="email"
                class="w-full px-3 py-2 rounded-lg text-sm mt-1 transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                required />
            @error('email') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Password (optional) --}}
        <div>
            <label class="text-xs text-muted">{{ __('Password') }} <span class="text-muted">{{ __('(kosongkan jika tidak diubah)') }}</span></label>
            <input wire:model="password" type="password"
                class="w-full px-3 py-2 rounded-lg text-sm mt-1 transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                autocomplete="new-password" />
            @error('password') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Password Confirmation --}}
        <div>
            <label class="text-xs text-muted">{{ __('Konfirmasi Password') }}</label>
            <input wire:model="password_confirmation" type="password"
                class="w-full px-3 py-2 rounded-lg text-sm mt-1 transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                autocomplete="new-password" />
        </div>

        {{-- NIK --}}
        <div>
            <label class="text-xs text-muted">NIK</label>
            <input wire:model="nik" type="text"
                class="w-full px-3 py-2 rounded-lg text-sm mt-1 transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);" />
            @error('nik') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Department & Business Unit --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs text-muted">Department</label>
                <input wire:model="department" type="text"
                    class="w-full px-3 py-2 rounded-lg text-sm mt-1 transition-colors duration-200"
                    style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);" />
            </div>
            <div>
                <label class="text-xs text-muted">{{ __('Corp Unit') }}</label>
                <select wire:model="business_unit"
                    class="w-full px-3 py-2 rounded-lg text-sm mt-1 transition-colors duration-200"
                    style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);">
                    <option value="">{{ __('Pilih Corp Unit') }}</option>
                    @foreach($this->getBusinessUnitList() as $code => $label)
                        <option value="{{ $code }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('business_unit') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Site & No. Telepon --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs text-muted">{{ __('Site') }}</label>
                <select wire:model="site"
                    class="w-full px-3 py-2 rounded-lg text-sm mt-1 transition-colors duration-200"
                    style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);">
                    <option value="">{{ __('Pilih Site') }}</option>
                    @foreach($this->getSiteList() as $idSite => $label)
                        <option value="{{ $idSite }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('site') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs text-muted">{{ __('No. Telepon') }}</label>
                <input wire:model="no_telepon" type="text"
                    class="w-full px-3 py-2 rounded-lg text-sm mt-1 transition-colors duration-200"
                    style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);" />
            </div>
        </div>

        {{-- Status --}}
        <div>
            <label class="text-xs text-muted">{{ __('Status Employee') }}</label>
            <select wire:model="status"
                class="w-full px-3 py-2 rounded-lg text-sm mt-1 transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);">
                <option value="active">Active</option>
                <option value="resigned">Resigned</option>
            </select>
            @error('status') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror

            @if ($this->status === 'resigned' && count($assignedAssets) > 0)
                <div class="p-3 mt-3 rounded-lg text-sm"
                    style="background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.35); color: var(--color-text-primary);">
                    <p class="font-semibold" style="color: #ef4444;">{{ __('User masih memiliki asset terpasang') }}</p>
                    <p class="mt-1" style="color: var(--color-text-secondary);">
                        {{ __('Sebelum mengubah status menjadi Resigned, kembalikan terlebih dahulu') }}
                        {{ count($assignedAssets) }} {{ __('asset berikut melalui Form Pengembalian Asset') }}:
                    </p>
                    <ul class="mt-2 space-y-1 text-xs" style="color: var(--color-text-secondary);">
                        @foreach ($assignedAssets as $asset)
                            <li>• {{ $asset['no_asset'] ?? '-' }} — {{ $asset['nama_perangkat'] ?? '-' }}
                                @if (($asset['brand'] ?? '') || ($asset['tipe'] ?? ''))
                                    ({{ $asset['brand'] ?? '' }} {{ $asset['tipe'] ?? '' }})
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('admin.pengembalian.create', ['user' => $this->user->id]) }}" wire:navigate
                        class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-200"
                        style="background: var(--color-primary); color: var(--color-button-text);">
                        {{ __('Buat Form Pengembalian Asset') }}
                    </a>
                </div>
            @endif
        </div>

        {{-- Role --}}
        <div>
            <label class="text-xs text-muted">{{ __('Role') }} <span class="text-red-400">*</span></label>
            <select wire:model="role"
                class="w-full px-3 py-2 rounded-lg text-sm mt-1 transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);"
                required>
                <option value="">{{ __('Pilih Role') }}</option>
                @foreach($this->getRoleList() as $r)
                    <option value="{{ $r }}">{{ $this->getRoleLabel($r) }}</option>
                @endforeach
            </select>
            @error('role') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" wire:loading.attr="disabled"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200"
                style="background: var(--color-primary); color: var(--color-button-text);">
                <span wire:loading.remove wire:target="update">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </span>
                <span wire:loading wire:target="update">{{ __('Menyimpan') }}...</span>
                <span wire:loading.remove wire:target="update">{{ __('Simpan Perubahan') }}</span>
            </button>
            <a href="{{ route('admin.users.index') }}" wire:navigate
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200"
                style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                {{ __('Batal') }}
            </a>
        </div>
    </form>
</div>
