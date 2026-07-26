@props(['header' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ Auth::user()->theme_preference ?? 'light' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        <meta name="theme-color" content="#0a0a0a">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

        <title>Admin Panel - {{ config('app.name', 'ASRI Form Perangkat') }}</title>

        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <link rel="apple-touch-icon" href="{{ asset('icon-192.svg') }}">
        <link rel="icon" href="{{ asset('icon-192.svg') }}" type="image/svg+xml">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const html = document.documentElement;
                html.style.transition = 'background-color 0.3s ease, color 0.3s ease';

                window.addEventListener('theme-changed', (e) => {
                    html.classList.toggle('dark', e.detail.theme === 'dark');
                });
            });

            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').catch(() => {});
                });
            }
        </script>
    </head>
    <body class="font-sans antialiased transition-colors duration-300" style="background-color: var(--color-bg-primary); color: var(--color-text-primary);">
        <div class="min-h-screen pt-16" style="background-color: var(--color-bg-primary);">
            <livewire:layout.navigation />

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col lg:flex-row gap-6">
                    {{-- Sidebar --}}
                    <aside class="w-full lg:w-56 shrink-0">
                        <div class="glass-card p-2 sticky top-24">
                            <div class="px-3 py-2 mb-1">
                                <h2 class="text-sm font-bold text-primary">Admin Panel</h2>
                            </div>
                            <nav class="space-y-0.5">
                                <a href="{{ route('admin.dashboard') }}" wire:navigate
                                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'admin-nav-active' : '' }}"
                                    style="{{ request()->routeIs('admin.dashboard') ? '' : 'color: var(--color-text-secondary);' }}">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.sites.index') }}" wire:navigate
                                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 {{ request()->routeIs('admin.sites.*') ? 'admin-nav-active' : '' }}"
                                    style="{{ request()->routeIs('admin.sites.*') ? '' : 'color: var(--color-text-secondary);' }}">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Sites
                                </a>
                                <a href="{{ route('admin.users.index') }}" wire:navigate
                                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 {{ request()->routeIs('admin.users.*') ? 'admin-nav-active' : '' }}"
                                    style="{{ request()->routeIs('admin.users.*') ? '' : 'color: var(--color-text-secondary);' }}">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    Users
                                </a>
                                <a href="{{ route('admin.assets.index') }}" wire:navigate
                                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 {{ request()->routeIs('admin.assets.*') ? 'admin-nav-active' : '' }}"
                                    style="{{ request()->routeIs('admin.assets.*') ? '' : 'color: var(--color-text-secondary);' }}">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    Assets
                                </a>
                            </nav>
                        </div>
                    </aside>

                    {{-- Main Content --}}
                    <div class="flex-1 min-w-0">
                        @if (isset($header) && $header !== null)
                            <div class="mb-6">
                                {{ $header }}
                            </div>
                        @endif

                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardBarChart', (refName, labels, data, bgColor, borderColor) => ({
            chart: null,
            init() {
                const ctx = this.$refs[refName];
                if (!ctx || typeof Chart === 'undefined') return;
                this.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah',
                            data: data,
                            backgroundColor: bgColor,
                            borderColor: borderColor,
                            borderWidth: 1,
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: { legend: { display: false } },
                        scales: {
                            x: {
                                ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--color-text-muted').trim(), stepSize: 1 },
                                grid: { color: getComputedStyle(document.documentElement).getPropertyValue('--color-border').trim() }
                            },
                            y: {
                                ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--color-text-muted').trim(), font: { size: 11 } },
                                grid: { display: false }
                            }
                        }
                    }
                });
            },
            destroy() { if (this.chart) this.chart.destroy(); }
        }));

        Alpine.data('dashboardLineChart', (refName, labels, data) => ({
            chart: null,
            init() {
                const ctx = this.$refs[refName];
                if (!ctx || typeof Chart === 'undefined') return;
                this.chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Perawatan',
                            data: data,
                            borderColor: 'rgba(168, 85, 247, 1)',
                            backgroundColor: 'rgba(168, 85, 247, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgba(168, 85, 247, 1)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--color-text-muted').trim(), font: { size: 10 } }, grid: { display: false } },
                            y: { ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--color-text-muted').trim(), stepSize: 1 }, grid: { color: getComputedStyle(document.documentElement).getPropertyValue('--color-border').trim() } }
                        }
                    }
                });
            },
            destroy() { if (this.chart) this.chart.destroy(); }
        }));
    });
    </script>
</html>
