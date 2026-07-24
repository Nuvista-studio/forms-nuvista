<?php

use App\Livewire\Actions\Logout;
use App\Livewire\Layout\NotificationBell;
use Livewire\Volt\Component;
use Livewire\Livewire;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    public function toggleTheme(): void
    {
        $user = auth()->user();
        $newTheme = $user->theme_preference === 'dark' ? 'light' : 'dark';
        $user->update(['theme_preference' => $newTheme]);

        $this->dispatch('theme-changed', theme: $newTheme);
    }
}; ?>

<div>
<nav x-data="{ open: false }" class="glass-nav sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-2">
                        <span class="text-lg font-bold text-primary">ASRI</span>
                        <span class="hidden sm:inline text-sm text-secondary">Form Perangkat</span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @if(auth()->user()->hasAnyRole(['admin', 'teknisi']))
                        <x-nav-link :href="route('pemeriksaan.create')" :active="request()->routeIs('pemeriksaan.*')" wire:navigate>
                            {{ __('Form Pemeriksaan') }}
                        </x-nav-link>
                        <x-nav-link :href="route('perawatan.create')" :active="request()->routeIs('perawatan.*')" wire:navigate>
                            {{ __('Form Perawatan') }}
                        </x-nav-link>
                    @endif
                    <x-nav-link :href="route('forms.search')" :active="request()->routeIs('forms.*')" wire:navigate>
                        {{ __('Cari Form') }}
                    </x-nav-link>
                    @if(auth()->user()->hasRole('admin'))
                        <x-nav-link :href="url('/admin')" :active="request()->is('admin*')">
                            {{ __('Admin Panel') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                <livewire:layout.notification-bell />

                <button
                    wire:click="toggleTheme"
                    x-data
                    x-on:theme-changed.window="$el.closest('html').classList.toggle('dark', $event.detail.theme === 'dark')"
                    class="p-2 rounded-lg transition-colors duration-200"
                    style="color: var(--color-text-secondary);"
                    title="Toggle Dark Mode"
                >
                    <svg x-data x-show="$el.closest('html').classList.contains('dark')" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg x-data x-show="!$el.closest('html').classList.contains('dark')" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm leading-4 font-medium rounded-lg transition-colors duration-200" style="color: var(--color-text-secondary);">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                            <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b" style="border-color: var(--color-border);">
                            <div class="text-sm font-medium text-primary">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-muted">{{ auth()->user()->email }}</div>
                        </div>
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden gap-1">
                <livewire:layout.notification-bell />
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md transition duration-150 ease-in-out" style="color: var(--color-text-secondary);">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @if(auth()->user()->hasAnyRole(['admin', 'teknisi']))
                <x-responsive-nav-link :href="route('pemeriksaan.create')" :active="request()->routeIs('pemeriksaan.*')" wire:navigate>
                    {{ __('Form Pemeriksaan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('perawatan.create')" :active="request()->routeIs('perawatan.*')" wire:navigate>
                    {{ __('Form Perawatan') }}
                </x-responsive-nav-link>
            @endif
            <x-responsive-nav-link :href="route('forms.search')" :active="request()->routeIs('forms.*')" wire:navigate>
                {{ __('Cari Form') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t" style="border-color: var(--color-border);">
            <div class="px-4 flex items-center justify-between">
                <div>
                    <div class="font-medium text-base text-primary" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                    <div class="font-medium text-sm text-muted">{{ auth()->user()->email }}</div>
                </div>
                <button wire:click="toggleTheme" class="p-2 rounded-lg" style="color: var(--color-text-secondary);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>

{{-- Mobile Bottom Navigation --}}
<nav class="bottom-nav sm:hidden" x-data="{ }">
    <div class="flex items-center justify-around h-14">
        <a href="{{ route('dashboard') }}" wire:navigate
            class="flex flex-col items-center gap-0.5 px-2 py-1 rounded-lg transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'text-blue-400' : '' }}"
            @unless(request()->routeIs('dashboard')) style="color: var(--color-text-secondary);" @endunless>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-[10px] font-medium">Home</span>
        </a>

        @if(auth()->user()->hasAnyRole(['admin', 'teknisi']))
            <a href="{{ route('pemeriksaan.create') }}" wire:navigate
                class="flex flex-col items-center gap-0.5 px-2 py-1 rounded-lg transition-colors duration-200 {{ request()->routeIs('pemeriksaan.*') ? 'text-blue-400' : '' }}"
                @unless(request()->routeIs('pemeriksaan.*')) style="color: var(--color-text-secondary);" @endunless>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-[10px] font-medium">Pemeriksaan</span>
            </a>
        @endif

        @if(auth()->user()->hasAnyRole(['admin', 'teknisi']))
            <a href="{{ route('perawatan.create') }}" wire:navigate
                class="flex flex-col items-center gap-0.5 px-2 py-1 rounded-lg transition-colors duration-200 {{ request()->routeIs('perawatan.*') ? 'text-purple-400' : '' }}"
                @unless(request()->routeIs('perawatan.*')) style="color: var(--color-text-secondary);" @endunless>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-[10px] font-medium">Perawatan</span>
            </a>
        @endif

        <a href="{{ route('forms.search') }}" wire:navigate
            class="flex flex-col items-center gap-0.5 px-2 py-1 rounded-lg transition-colors duration-200 {{ request()->routeIs('forms.*') ? 'text-emerald-400' : '' }}"
            @unless(request()->routeIs('forms.*')) style="color: var(--color-text-secondary);" @endunless>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span class="text-[10px] font-medium">Cari</span>
        </a>

        <a href="{{ route('profile') }}" wire:navigate
            class="flex flex-col items-center gap-0.5 px-2 py-1 rounded-lg transition-colors duration-200 {{ request()->routeIs('profile') ? 'text-amber-400' : '' }}"
            @unless(request()->routeIs('profile')) style="color: var(--color-text-secondary);" @endunless>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-[10px] font-medium">Profil</span>
        </a>
    </div>
</nav>
</div>
