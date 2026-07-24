<?php

use Livewire\Attributes\Layout;

new #[Layout('components.app-layout')] class extends Component {}; ?>

<div class="max-w-7xl mx-auto px-4 py-6 space-y-6">
    <h1 class="text-2xl font-bold text-primary">Dashboard</h1>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-card p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-500/15 flex items-center justify-center text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <p class="text-xs text-muted">Pemeriksaan</p>
                    <p class="text-2xl font-bold text-primary">{{ $totalPemeriksaan }}</p>
                </div>
            </div>
        </div>
        <div class="glass-card p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-500/15 flex items-center justify-center text-purple-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-muted">Perawatan</p>
                    <p class="text-2xl font-bold text-primary">{{ $totalPerawatan }}</p>
                </div>
            </div>
        </div>
        <div class="glass-card p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-500/15 flex items-center justify-center text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-muted">Pending Approval</p>
                    <p class="text-2xl font-bold text-primary">{{ $pendingApproval }}</p>
                </div>
            </div>
        </div>
        <div class="glass-card p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/15 flex items-center justify-center text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-muted">Selesai</p>
                    <p class="text-2xl font-bold text-primary">{{ $selesai }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Status Distribution --}}
        <div class="glass-card p-5">
            <h3 class="text-sm font-bold text-primary mb-4">Status Form</h3>
            <div x-data="{
                chart: null,
                init() {
                    const ctx = this.$refs.statusChart;
                    this.chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: @json(array_values($statusLabels)),
                            datasets: [
                                {
                                    label: 'Pemeriksaan',
                                    data: @json(array_values(array_map(fn($s) => $statusPemeriksaan[$s] ?? 0, array_keys($statusLabels)))),
                                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                                    borderColor: 'rgba(59, 130, 246, 1)',
                                    borderWidth: 1,
                                    borderRadius: 4,
                                },
                                {
                                    label: 'Perawatan',
                                    data: @json(array_values(array_map(fn($s) => $statusPerawatan[$s] ?? 0, array_keys($statusLabels)))),
                                    backgroundColor: 'rgba(168, 85, 247, 0.6)',
                                    borderColor: 'rgba(168, 85, 247, 1)',
                                    borderWidth: 1,
                                    borderRadius: 4,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { labels: { color: getComputedStyle(document.documentElement).getPropertyValue('--color-text-secondary').trim() } } },
                            scales: {
                                x: { ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--color-text-muted').trim(), font: { size: 10 } }, grid: { display: false } },
                                y: { ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--color-text-muted').trim(), stepSize: 1 }, grid: { color: getComputedStyle(document.documentElement).getPropertyValue('--color-border').trim() } }
                            }
                        }
                    });
                }
            }" style="height: 250px;">
                <canvas x-ref="statusChart"></canvas>
            </div>
        </div>

        {{-- Kondisi Distribusi --}}
        <div class="glass-card p-5">
            <h3 class="text-sm font-bold text-primary mb-4">Distribusi Kondisi</h3>
            <div x-data="{
                chart: null,
                init() {
                    const ctx = this.$refs.kondisiChart;
                    this.chart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: @json(array_values($kondisiLabels)),
                            datasets: [{
                                data: @json(array_values(array_map(fn($k) => $kondisiDistribusi[$k] ?? 0, array_keys($kondisiLabels)))),
                                backgroundColor: [
                                    'rgba(16, 185, 129, 0.7)',
                                    'rgba(245, 158, 11, 0.7)',
                                    'rgba(59, 130, 246, 0.7)',
                                    'rgba(239, 68, 68, 0.7)',
                                ],
                                borderColor: [
                                    'rgba(16, 185, 129, 1)',
                                    'rgba(245, 158, 11, 1)',
                                    'rgba(59, 130, 246, 1)',
                                    'rgba(239, 68, 68, 1)',
                                ],
                                borderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '60%',
                            plugins: {
                                legend: { position: 'bottom', labels: { color: getComputedStyle(document.documentElement).getPropertyValue('--color-text-secondary').trim(), padding: 12, font: { size: 11 } } }
                            }
                        }
                    });
                }
            }" style="height: 250px;">
                <canvas x-ref="kondisiChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Trend Perawatan --}}
    <div class="glass-card p-5">
        <h3 class="text-sm font-bold text-primary mb-4">Tren Perawatan per Bulan (12 Bulan)</h3>
        <div x-data="{
            chart: null,
            init() {
                const ctx = this.$refs.trendChart;
                this.chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json(array_keys($trendPerawatan)),
                        datasets: [{
                            label: 'Jumlah Perawatan',
                            data: @json(array_values($trendPerawatan)),
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
            }
        }" style="height: 220px;">
            <canvas x-ref="trendChart"></canvas>
        </div>
    </div>

    {{-- Recent Forms --}}
    <div class="glass-card p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-primary">Form Terbaru</h3>
            <a href="{{ route('forms.search') }}" wire:navigate class="text-xs text-blue-400 hover:underline">Lihat Semua &rarr;</a>
        </div>
        @if(count($recentForms) === 0)
            <p class="text-sm text-muted text-center py-6">Belum ada form</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b" style="border-color: var(--color-border);">
                            <th class="text-left py-2 text-xs text-muted font-medium">No. Form</th>
                            <th class="text-left py-2 text-xs text-muted font-medium">Tipe</th>
                            <th class="text-left py-2 text-xs text-muted font-medium">Teknisi</th>
                            <th class="text-left py-2 text-xs text-muted font-medium">Perangkat</th>
                            <th class="text-left py-2 text-xs text-muted font-medium">Status</th>
                            <th class="text-left py-2 text-xs text-muted font-medium">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--color-border);">
                        @foreach($recentForms as $form)
                            <tr class="transition-colors" onmouseover="this.style.backgroundColor='var(--color-bg-tertiary)'" onmouseout="this.style.backgroundColor=''">
                                <td class="py-2.5 font-mono font-semibold text-primary text-xs">{{ $form['nomor_form'] }}</td>
                                <td class="py-2.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold
                                        {{ $form['type'] === 'Pemeriksaan' ? 'bg-blue-500/15 text-blue-400' : 'bg-purple-500/15 text-purple-400' }}">
                                        {{ $form['type'] }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-primary">{{ $form['teknisi'] }}</td>
                                <td class="py-2.5 text-primary">{{ $form['perangkat'] }}</td>
                                <td class="py-2.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $this->getStatusColor($form['status']) }}">
                                        {{ ucfirst($form['status']) }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-muted text-xs">{{ $form['date'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
