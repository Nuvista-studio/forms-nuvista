<?php

namespace App\Livewire\Dashboard;

use App\Models\Asset;
use App\Models\FormPemeriksaan;
use App\Models\FormPerawatan;
use App\Models\Site;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public string $startDate = '';
    public string $endDate = '';
    public ?string $filterOperatingUnit = '';

    public array $perawatanBySite = [];
    public array $pemeriksaanBySite = [];
    public array $topAssets = [];
    public array $trendPerawatanBulanan = [];
    public array $operatingUnits = [];

    public function mount(): void
    {
        $this->endDate = now()->format('Y-m-d');
        $this->startDate = now()->subDays(29)->format('Y-m-d');
        $this->operatingUnits = Site::whereIn('id_site', Asset::whereNotNull('operating_unit')
            ->where('operating_unit', '!=', '')
            ->pluck('operating_unit'))
            ->orderBy('site')
            ->get()
            ->map(fn($s) => ['id' => $s->id_site, 'name' => $s->site])
            ->toArray();
        $this->loadAll();
    }

    public function updatedStartDate(): void
    {
        $this->loadAll();
    }

    public function updatedEndDate(): void
    {
        $this->loadAll();
    }

    public function updatedFilterOperatingUnit(): void
    {
        $this->loadTopAssets();
    }

    private function loadAll(): void
    {
        $this->loadPerawatanBySite();
        $this->loadPemeriksaanBySite();
        $this->loadTopAssets();
        $this->loadTrendPerawatanBulanan();
    }

    private function loadPerawatanBySite(): void
    {
        $start = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : now()->subDays(29)->startOfDay();
        $end = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : now()->endOfDay();

        $data = FormPerawatan::whereNotNull('submitted_at')
            ->whereBetween('submitted_at', [$start, $end])
            ->whereNotNull('site_location')
            ->select('site_location', DB::raw('count(*) as total'))
            ->groupBy('site_location')
            ->pluck('total', 'site_location')
            ->toArray();

        $siteNames = Site::whereIn('id_site', array_keys($data))
            ->pluck('site', 'id_site')
            ->toArray();

        $result = [];
        foreach ($data as $siteId => $count) {
            $result[] = [
                'site' => $siteNames[$siteId] ?? $siteId,
                'total' => (int) $count,
            ];
        }

        usort($result, fn($a, $b) => $b['total'] <=> $a['total']);
        $this->perawatanBySite = $result;
    }

    private function loadPemeriksaanBySite(): void
    {
        $start = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : now()->subDays(29)->startOfDay();
        $end = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : now()->endOfDay();

        $data = FormPemeriksaan::whereNotNull('submitted_at')
            ->whereBetween('submitted_at', [$start, $end])
            ->whereNotNull('site_location')
            ->select('site_location', DB::raw('count(*) as total'))
            ->groupBy('site_location')
            ->pluck('total', 'site_location')
            ->toArray();

        $siteNames = Site::whereIn('id_site', array_keys($data))
            ->pluck('site', 'id_site')
            ->toArray();

        $result = [];
        foreach ($data as $siteId => $count) {
            $result[] = [
                'site' => $siteNames[$siteId] ?? $siteId,
                'total' => (int) $count,
            ];
        }

        usort($result, fn($a, $b) => $b['total'] <=> $a['total']);
        $this->pemeriksaanBySite = $result;
    }

    private function loadTopAssets(): void
    {
        $query = Asset::select('assets.id', 'assets.nama_perangkat', 'assets.no_asset', 'assets.operating_unit', 'assets.site_location_asset')
            ->join('form_pemeriksaan', 'assets.id', '=', 'form_pemeriksaan.asset_id')
            ->selectRaw('assets.id, assets.nama_perangkat, assets.no_asset, assets.operating_unit, assets.site_location_asset, count(form_pemeriksaan.id) as total_pemeriksaan')
            ->groupBy('assets.id', 'assets.nama_perangkat', 'assets.no_asset', 'assets.operating_unit', 'assets.site_location_asset');

        if ($this->filterOperatingUnit) {
            $query->where('assets.operating_unit', $this->filterOperatingUnit);
        }

        $topAssets = $query->orderByDesc('total_pemeriksaan')
            ->limit(10)
            ->get()
            ->toArray();

        $allSiteIds = collect($topAssets)
            ->pluck('site_location_asset')
            ->merge(collect($topAssets)->pluck('operating_unit'))
            ->filter()
            ->unique()
            ->toArray();

        $siteNames = Site::whereIn('id_site', $allSiteIds)
            ->pluck('site', 'id_site')
            ->toArray();

        $result = [];
        foreach ($topAssets as $a) {
            $result[] = [
                'id' => $a['id'],
                'nama_perangkat' => $a['nama_perangkat'],
                'no_asset' => $a['no_asset'],
                'operating_unit' => $siteNames[$a['operating_unit']] ?? ($a['operating_unit'] ?? '-'),
                'site_location' => $siteNames[$a['site_location_asset']] ?? ($a['site_location_asset'] ?? '-'),
                'total' => (int) $a['total_pemeriksaan'],
            ];
        }

        $this->topAssets = $result;
    }

    private function loadTrendPerawatanBulanan(): void
    {
        $trend = FormPerawatan::whereNotNull('submitted_at')
            ->select(
                DB::raw("DATE_FORMAT(submitted_at, '%Y-%m') as month"),
                DB::raw('count(*) as total')
            )
            ->where('submitted_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $this->trendPerawatanBulanan = $trend;
    }

    public function render()
    {
        return view('livewire.dashboard.index');
    }
}
