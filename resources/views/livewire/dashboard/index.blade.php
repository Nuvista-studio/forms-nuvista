<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-primary">Dashboard</h1>
            <p class="text-sm text-muted mt-1">Laporan & Analitik</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <label class="text-xs text-muted">Dari:</label>
            <input wire:model.live.debounce.500ms="startDate" type="date"
                class="px-3 py-1.5 rounded-lg text-xs transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);" />
            <label class="text-xs text-muted">Sampai:</label>
            <input wire:model.live.debounce.500ms="endDate" type="date"
                class="px-3 py-1.5 rounded-lg text-xs transition-colors duration-200"
                style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);" />
        </div>
    </div>

    {{-- Report 1: Perawatan by Site --}}
    <div class="glass-card p-5">
        <h3 class="text-sm font-bold text-primary mb-4">Laporan Perawatan Perangkat by Site Lokasi</h3>
        @if(count($perawatanBySite) > 0)
            @php
                $pwLabels = json_encode(array_column($perawatanBySite, 'site'));
                $pwData = json_encode(array_column($perawatanBySite, 'total'));
            @endphp
            <div x-data="{
                chart: null,
                init() {
                    this.$nextTick(() => {
                        const ctx = this.$refs.chartPerawatan;
                        if (!ctx || typeof Chart === 'undefined') return;
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: {{ $pwLabels }},
                                datasets: [{
                                    label: 'Jumlah Perawatan',
                                    data: {{ $pwData }},
                                    backgroundColor: 'rgba(168, 85, 247, 0.6)',
                                    borderColor: 'rgba(168, 85, 247, 1)',
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
                                    x: { ticks: { color: 'rgb(156,163,175)', stepSize: 1 }, grid: { color: 'rgb(229,231,235)' } },
                                    y: { ticks: { color: 'rgb(156,163,175)', font: { size: 11 } }, grid: { display: false } }
                                }
                            }
                        });
                    });
                },
                destroy() { if (this.chart) this.chart.destroy(); }
            }" style="height: {{ max(200, count($perawatanBySite) * 40 + 60) }}px;">
                <canvas x-ref="chartPerawatan"></canvas>
            </div>
        @else
            <p class="text-sm text-muted text-center py-4">Tidak ada data perawatan pada periode ini</p>
        @endif
        <div class="mt-3 text-right">
            <a href="{{ route('admin.perawatan.index') }}" wire:navigate class="text-xs font-medium transition-colors duration-200" style="color: var(--color-primary);">
                Lihat Semua Perawatan →
            </a>
        </div>
    </div>

    {{-- Report 2: Pemeriksaan by Site --}}
    <div class="glass-card p-5">
        <h3 class="text-sm font-bold text-primary mb-4">Laporan Pemeriksaan Perangkat by Site Lokasi</h3>
        @if(count($pemeriksaanBySite) > 0)
            @php
                $pmLabels = json_encode(array_column($pemeriksaanBySite, 'site'));
                $pmData = json_encode(array_column($pemeriksaanBySite, 'total'));
            @endphp
            <div x-data="{
                chart: null,
                init() {
                    this.$nextTick(() => {
                        const ctx = this.$refs.chartPemeriksaan;
                        if (!ctx || typeof Chart === 'undefined') return;
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: {{ $pmLabels }},
                                datasets: [{
                                    label: 'Jumlah Pemeriksaan',
                                    data: {{ $pmData }},
                                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                                    borderColor: 'rgba(59, 130, 246, 1)',
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
                                    x: { ticks: { color: 'rgb(156,163,175)', stepSize: 1 }, grid: { color: 'rgb(229,231,235)' } },
                                    y: { ticks: { color: 'rgb(156,163,175)', font: { size: 11 } }, grid: { display: false } }
                                }
                            }
                        });
                    });
                },
                destroy() { if (this.chart) this.chart.destroy(); }
            }" style="height: {{ max(200, count($pemeriksaanBySite) * 40 + 60) }}px;">
                <canvas x-ref="chartPemeriksaan"></canvas>
            </div>
        @else
            <p class="text-sm text-muted text-center py-4">Tidak ada data pemeriksaan pada periode ini</p>
        @endif
        <div class="mt-3 text-right">
            <a href="{{ route('admin.pemeriksaan.index') }}" wire:navigate class="text-xs font-medium transition-colors duration-200" style="color: var(--color-primary);">
                Lihat Semua Pemeriksaan →
            </a>
        </div>
    </div>

    {{-- Report 3: Top 10 Assets --}}
    <div class="glass-card p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <h3 class="text-sm font-bold text-primary">10 Perangkat yang Sering Diperiksa</h3>
            <div class="flex items-center gap-2">
                <label class="text-xs text-muted">Operating Unit:</label>
                <select wire:model.live.debounce.500ms="filterOperatingUnit"
                    class="px-3 py-1.5 rounded-lg text-xs transition-colors duration-200"
                    style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);">
                    <option value="">Semua</option>
                    @foreach($operatingUnits as $ou)
                        <option value="{{ $ou['id'] }}">{{ $ou['name'] }} ({{ $ou['id'] }})</option>
                    @endforeach
                </select>
            </div>
        </div>
        @if(count($topAssets) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b" style="border-color: var(--color-border);">
                            <th class="text-left py-2 text-xs text-muted font-medium">#</th>
                            <th class="text-left py-2 text-xs text-muted font-medium">No Asset</th>
                            <th class="text-left py-2 text-xs text-muted font-medium">Nama Perangkat</th>
                            <th class="text-left py-2 text-xs text-muted font-medium hidden md:table-cell">Operating Unit</th>
                            <th class="text-left py-2 text-xs text-muted font-medium hidden lg:table-cell">Site Location</th>
                            <th class="text-right py-2 text-xs text-muted font-medium">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--color-border);">
                        @foreach($topAssets as $i => $a)
                            <tr class="transition-colors cursor-pointer" onclick="window.Livewire.navigate('{{ route('admin.assets.index', ['search' => $a['no_asset']]) }}')" onmouseover="this.style.backgroundColor='var(--color-bg-tertiary)'" onmouseout="this.style.backgroundColor=''">
                                <td class="py-2.5 text-muted text-xs">{{ $i + 1 }}</td>
                                <td class="py-2.5 font-mono text-secondary text-xs">{{ $a['no_asset'] }}</td>
                                <td class="py-2.5 font-medium text-primary">{{ $a['nama_perangkat'] }}</td>
                                <td class="py-2.5 text-secondary text-xs hidden md:table-cell">{{ $a['operating_unit'] }}</td>
                                <td class="py-2.5 text-secondary text-xs hidden lg:table-cell">{{ $a['site_location'] }}</td>
                                <td class="py-2.5 text-right">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-500/15 text-blue-400">
                                        {{ $a['total'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-right">
                <a href="{{ route('admin.assets.index') }}" wire:navigate class="text-xs font-medium transition-colors duration-200" style="color: var(--color-primary);">
                    Lihat Semua Asset →
                </a>
            </div>
        @else
            <p class="text-sm text-muted text-center py-4">Tidak ada data pemeriksaan asset</p>
        @endif
    </div>

    {{-- Report 4: Tren Perawatan per Bulan --}}
    <div class="glass-card p-5">
        <h3 class="text-sm font-bold text-primary mb-4">Tren Perawatan per Bulan (12 Bulan)</h3>
        @if(count($trendPerawatanBulanan) > 0)
            @php
                $trLabels = json_encode(array_keys($trendPerawatanBulanan));
                $trData = json_encode(array_values($trendPerawatanBulanan));
            @endphp
            <div x-data="{
                chart: null,
                init() {
                    this.$nextTick(() => {
                        const ctx = this.$refs.chartTrend;
                        if (!ctx || typeof Chart === 'undefined') return;
                        this.chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: {{ $trLabels }},
                                datasets: [{
                                    label: 'Jumlah Perawatan',
                                    data: {{ $trData }},
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
                                    x: { ticks: { color: 'rgb(156,163,175)', font: { size: 10 } }, grid: { display: false } },
                                    y: { ticks: { color: 'rgb(156,163,175)', stepSize: 1 }, grid: { color: 'rgb(229,231,235)' } }
                                }
                            }
                        });
                    });
                },
                destroy() { if (this.chart) this.chart.destroy(); }
            }" style="height: 220px;">
                <canvas x-ref="chartTrend"></canvas>
            </div>
        @else
            <p class="text-sm text-muted text-center py-4">Tidak ada data tren perawatan</p>
        @endif
        <div class="mt-3 text-right">
            <a href="{{ route('admin.perawatan.index') }}" wire:navigate class="text-xs font-medium transition-colors duration-200" style="color: var(--color-primary);">
                Lihat Semua Perawatan →
            </a>
        </div>
    </div>

    {{-- Report 5: Perawatan vs Belum by Operating Unit --}}
    <div class="glass-card p-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <h3 class="text-sm font-bold text-primary">Perangkat Dilakukan Perawatan vs Belum Perawatan by Operating Unit</h3>
            <div class="flex items-center gap-2">
                <label class="text-xs text-muted">Status Asset:</label>
                <select wire:model.live.debounce.300ms="filterAssetStatus"
                    class="px-3 py-1.5 rounded-lg text-xs transition-colors duration-200"
                    style="background: var(--color-input-bg, var(--color-glass-bg)); border: 1px solid var(--color-border); color: var(--color-text-primary);">
                    <option value="">Semua</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
        @if(count($perawatanVsBelum) > 0)
            @php
                $pvbLabels = json_encode(array_column($perawatanVsBelum, 'operating_unit'));
                $pvbDilakukan = json_encode(array_column($perawatanVsBelum, 'dilakukan'));
                $pvbBelum = json_encode(array_column($perawatanVsBelum, 'belum'));
                $chartHeight = max(260, count($perawatanVsBelum) * 50 + 80);
            @endphp
            <div x-data="{
                chart: null,
                init() {
                    this.$nextTick(() => {
                        const ctx = this.$refs.chartPerawatanVsBelum;
                        if (!ctx || typeof Chart === 'undefined') return;
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: {{ $pvbLabels }},
                                datasets: [
                                    {
                                        label: 'Dilakukan Perawatan',
                                        data: {{ $pvbDilakukan }},
                                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                                        borderColor: 'rgba(16, 185, 129, 1)',
                                        borderWidth: 1,
                                        borderRadius: 4,
                                    },
                                    {
                                        label: 'Belum Perawatan',
                                        data: {{ $pvbBelum }},
                                        backgroundColor: 'rgba(239, 68, 68, 0.5)',
                                        borderColor: 'rgba(239, 68, 68, 1)',
                                        borderWidth: 1,
                                        borderRadius: 4,
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                indexAxis: 'y',
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                        labels: { color: 'rgb(156,163,175)', font: { size: 11 }, usePointStyle: true, pointStyle: 'rectRounded' }
                                    }
                                },
                                scales: {
                                    x: {
                                        stacked: true,
                                        ticks: { color: 'rgb(156,163,175)', stepSize: 1 },
                                        grid: { color: 'rgb(229,231,235)' },
                                        title: { display: true, text: 'Jumlah Asset', color: 'rgb(156,163,175)', font: { size: 11 } }
                                    },
                                    y: {
                                        stacked: true,
                                        ticks: { color: 'rgb(156,163,175)', font: { size: 11 } },
                                        grid: { display: false }
                                    }
                                }
                            }
                        });
                    });
                },
                destroy() { if (this.chart) this.chart.destroy(); }
            }" style="height: {{ $chartHeight }}px;">
                <canvas x-ref="chartPerawatanVsBelum"></canvas>
            </div>

            {{-- Summary Table --}}
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b" style="border-color: var(--color-border);">
                            <th class="text-left py-2 text-xs text-muted font-medium">Operating Unit</th>
                            <th class="text-right py-2 text-xs text-muted font-medium">Total Asset</th>
                            <th class="text-right py-2 text-xs text-muted font-medium">Dilakukan</th>
                            <th class="text-right py-2 text-xs text-muted font-medium">Belum</th>
                            <th class="text-right py-2 text-xs text-muted font-medium">% Selesai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--color-border);">
                        @foreach($perawatanVsBelum as $row)
                            @php
                                $pct = $row['total'] > 0 ? round(($row['dilakukan'] / $row['total']) * 100, 1) : 0;
                                $ouId = $row['operating_unit_id'] ?? '';
                            @endphp
                            <tr class="transition-colors" onmouseover="this.style.backgroundColor='var(--color-bg-tertiary)'" onmouseout="this.style.backgroundColor=''">
                                <td class="py-2.5 font-medium text-primary">{{ $row['operating_unit'] }}</td>
                                <td class="py-2.5 text-right text-secondary">
                                    <a href="{{ route('admin.assets.index', ['filterOperatingUnit' => $ouId]) }}" wire:navigate class="hover:underline font-semibold" style="color: var(--color-text-secondary);">
                                        {{ $row['total'] }}
                                    </a>
                                </td>
                                <td class="py-2.5 text-right">
                                    <a href="{{ route('admin.assets.index', ['filterOperatingUnit' => $ouId, 'filterPerawatanStatus' => 'done']) }}" wire:navigate class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold transition-opacity hover:opacity-70" style="background: rgba(16,185,129,0.15); color: #10b981;">
                                        {{ $row['dilakukan'] }}
                                    </a>
                                </td>
                                <td class="py-2.5 text-right">
                                    <a href="{{ route('admin.assets.index', ['filterOperatingUnit' => $ouId, 'filterPerawatanStatus' => 'pending']) }}" wire:navigate class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold transition-opacity hover:opacity-70" style="background: rgba(239,68,68,0.15); color: #ef4444;">
                                        {{ $row['belum'] }}
                                    </a>
                                </td>
                                <td class="py-2.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <div class="w-16 h-1.5 rounded-full overflow-hidden" style="background: var(--color-bg-tertiary);">
                                            <div class="h-full rounded-full" style="width: {{ $pct }}%; background: {{ $pct >= 80 ? '#10b981' : ($pct >= 50 ? '#eab308' : '#ef4444') }};"></div>
                                        </div>
                                        <span class="text-xs text-secondary w-10 text-right">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-muted text-center py-4">Tidak ada data asset</p>
        @endif
    </div>
</div>
