<?php

namespace App\Livewire\Dashboard;

use App\Models\FormPemeriksaan;
use App\Models\FormPerawatan;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public int $totalPemeriksaan = 0;
    public int $totalPerawatan = 0;
    public int $pendingApproval = 0;
    public int $selesai = 0;
    public array $statusPemeriksaan = [];
    public array $statusPerawatan = [];
    public array $kondisiDistribusi = [];
    public array $trendPerawatan = [];
    public array $recentForms = [];

    public function mount(): void
    {
        $this->loadStats();
        $this->loadStatusDistribution();
        $this->loadKondisiDistribusi();
        $this->loadTrendPerawatan();
        $this->loadRecentForms();
    }

    private function loadStats(): void
    {
        $this->totalPemeriksaan = FormPemeriksaan::count();
        $this->totalPerawatan = FormPerawatan::count();
        $this->selesai = FormPemeriksaan::where('status', 'selesai')->count()
            + FormPerawatan::where('status', 'selesai')->count();
        $this->pendingApproval = FormPemeriksaan::whereIn('status', ['diketahui', 'disetujui'])->count()
            + FormPerawatan::whereIn('status', ['diketahui', 'disetujui'])->count();
    }

    private function loadStatusDistribution(): void
    {
        $pStatus = FormPemeriksaan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();
        $wStatus = FormPerawatan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();

        $this->statusPemeriksaan = $pStatus;
        $this->statusPerawatan = $wStatus;
    }

    private function loadKondisiDistribusi(): void
    {
        $pKondisi = FormPemeriksaan::whereNotNull('kondisi')
            ->select('kondisi', DB::raw('count(*) as total'))
            ->groupBy('kondisi')->pluck('total', 'kondisi')->toArray();

        $wKondisi = FormPerawatan::whereNotNull('kondisi_akhir')
            ->select('kondisi_akhir as kondisi', DB::raw('count(*) as total'))
            ->groupBy('kondisi_akhir')->pluck('total', 'kondisi')->toArray();

        $merged = [];
        foreach (['baru', 'lama', 'good_normal', 'caution_poor'] as $k) {
            $merged[$k] = ($pKondisi[$k] ?? 0) + ($wKondisi[$k] ?? 0);
        }

        $this->kondisiDistribusi = $merged;
    }

    private function loadTrendPerawatan(): void
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

        $this->trendPerawatan = $trend;
    }

    private function loadRecentForms(): void
    {
        $pForms = FormPemeriksaan::with(['teknisi', 'asset'])
            ->latest('submitted_at')
            ->limit(5)
            ->get()
            ->map(fn($f) => [
                'type' => 'Pemeriksaan',
                'nomor_form' => $f->nomor_form,
                'teknisi' => $f->teknisi->name ?? '-',
                'perangkat' => $f->asset->nama_perangkat ?? '-',
                'status' => $f->status,
                'date' => $f->submitted_at?->format('d M Y'),
                'id' => $f->id,
            ]);

        $wForms = FormPerawatan::with(['teknisi', 'asset'])
            ->latest('submitted_at')
            ->limit(5)
            ->get()
            ->map(fn($f) => [
                'type' => 'Perawatan',
                'nomor_form' => $f->nomor_form,
                'teknisi' => $f->teknisi->name ?? '-',
                'perangkat' => $f->asset->nama_perangkat ?? '-',
                'status' => $f->status,
                'date' => $f->submitted_at?->format('d M Y'),
                'id' => $f->id,
            ]);

        $this->recentForms = $pForms->concat($wForms)
            ->sortByDesc('date')
            ->values()
            ->toArray();
    }

    public function getStatusColor(string $status): string
    {
        return match ($status) {
            'draft' => 'bg-gray-500/15 text-gray-400',
            'submitted' => 'bg-blue-500/15 text-blue-400',
            'diketahui' => 'bg-yellow-500/15 text-yellow-400',
            'disetujui' => 'bg-green-500/15 text-green-400',
            'selesai' => 'bg-emerald-500/15 text-emerald-400',
            'revisi' => 'bg-red-500/15 text-red-400',
            default => 'bg-gray-500/15 text-gray-400',
        };
    }

    public function render()
    {
        return view('livewire.dashboard.index', [
            'statusLabels' => [
                'draft' => 'Draft',
                'submitted' => 'Submitted',
                'diketahui' => 'Diketahui',
                'disetujui' => 'Disetujui',
                'selesai' => 'Selesai',
                'revisi' => 'Revisi',
            ],
            'kondisiLabels' => [
                'baru' => 'Baru',
                'lama' => 'Lama',
                'good_normal' => 'Good / Normal',
                'caution_poor' => 'Caution / Poor',
            ],
        ])->layout('components.app-layout');
    }
}
