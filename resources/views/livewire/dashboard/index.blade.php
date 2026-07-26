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
            <div x-data="dashboardBarChart('perawatanSiteChart', @json(array_column($perawatanBySite, 'site')), @json(array_column($perawatanBySite, 'total')), 'rgba(168, 85, 247, 0.6)', 'rgba(168, 85, 247, 1)')"
                 wire:key="perawatan-{{ md5(json_encode($perawatanBySite)) }}"
                 style="height: {{ max(200, count($perawatanBySite) * 40 + 60) }}px;">
                <canvas x-ref="perawatanSiteChart"></canvas>
            </div>
        @else
            <p class="text-sm text-muted text-center py-4">Tidak ada data perawatan pada periode ini</p>
        @endif
    </div>

    {{-- Report 2: Pemeriksaan by Site --}}
    <div class="glass-card p-5">
        <h3 class="text-sm font-bold text-primary mb-4">Laporan Pemeriksaan Perangkat by Site Lokasi</h3>
        @if(count($pemeriksaanBySite) > 0)
            <div x-data="dashboardBarChart('pemeriksaanSiteChart', @json(array_column($pemeriksaanBySite, 'site')), @json(array_column($pemeriksaanBySite, 'total')), 'rgba(59, 130, 246, 0.6)', 'rgba(59, 130, 246, 1)')"
                 wire:key="pemeriksaan-{{ md5(json_encode($pemeriksaanBySite)) }}"
                 style="height: {{ max(200, count($pemeriksaanBySite) * 40 + 60) }}px;">
                <canvas x-ref="pemeriksaanSiteChart"></canvas>
            </div>
        @else
            <p class="text-sm text-muted text-center py-4">Tidak ada data pemeriksaan pada periode ini</p>
        @endif
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
                            <tr class="transition-colors" onmouseover="this.style.backgroundColor='var(--color-bg-tertiary)'" onmouseout="this.style.backgroundColor=''">
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
        @else
            <p class="text-sm text-muted text-center py-4">Tidak ada data pemeriksaan asset</p>
        @endif
    </div>

    {{-- Report 4: Tren Perawatan per Bulan --}}
    <div class="glass-card p-5">
        <h3 class="text-sm font-bold text-primary mb-4">Tren Perawatan per Bulan (12 Bulan)</h3>
        @if(count($trendPerawatanBulanan) > 0)
            <div x-data="dashboardLineChart('trendBulananChart', @json(array_keys($trendPerawatanBulanan)), @json(array_values($trendPerawatanBulanan)))"
                 wire:key="trend-{{ md5(json_encode($trendPerawatanBulanan)) }}"
                 style="height: 220px;">
                <canvas x-ref="trendBulananChart"></canvas>
            </div>
        @else
            <p class="text-sm text-muted text-center py-4">Tidak ada data tren perawatan</p>
        @endif
    </div>
</div>


