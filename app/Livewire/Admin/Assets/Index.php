<?php

namespace App\Livewire\Admin\Assets;

use App\Helpers\ActivityLogger;
use App\Models\Asset;
use App\Models\FormPerawatan;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterOperatingUnit = '';

    public string $filterPerawatanStatus = '';

    public bool $showDeleteModal = false;

    public ?int $deleteAssetId = null;

    public string $deleteAssetName = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterOperatingUnit' => ['except' => ''],
        'filterPerawatanStatus' => ['except' => ''],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterOperatingUnit(): void
    {
        $this->resetPage();
    }

    public function updatedFilterPerawatanStatus(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id, string $name): void
    {
        $this->deleteAssetId = $id;
        $this->deleteAssetName = $name;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteAssetId = null;
        $this->deleteAssetName = '';
    }

    public function deleteAsset(): void
    {
        Asset::find($this->deleteAssetId)->delete();

        ActivityLogger::log('delete', "Menghapus asset: {$this->deleteAssetName}", 'App\Models\Asset', $this->deleteAssetId);
        $this->cancelDelete();
        $this->dispatch('asset-deleted');
    }

    public function render()
    {
        $query = Asset::with('assignedUser', 'operatingUnitSite', 'siteAsset')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('no_asset', 'like', "%{$this->search}%")
                    ->orWhere('nama_perangkat', 'like', "%{$this->search}%")
                    ->orWhere('kategori', 'like', "%{$this->search}%")
                    ->orWhere('brand', 'like', "%{$this->search}%")
                    ->orWhere('tipe', 'like', "%{$this->search}%")
                    ->orWhere('no_serial', 'like', "%{$this->search}%");
            }));

        if ($this->filterOperatingUnit) {
            $query->where('operating_unit', $this->filterOperatingUnit);
        }

        if ($this->filterPerawatanStatus === 'done') {
            $assetIds = FormPerawatan::whereNotNull('submitted_at')
                ->distinct()->pluck('asset_id')->filter()->toArray();
            $query->whereIn('id', $assetIds);
        } elseif ($this->filterPerawatanStatus === 'pending') {
            $assetIds = FormPerawatan::whereNotNull('submitted_at')
                ->distinct()->pluck('asset_id')->filter()->toArray();
            $query->whereNotIn('id', $assetIds);
        }

        $assets = $query->orderBy('no_asset')->paginate(15);

        return view('livewire.admin.assets.index', [
            'assets' => $assets,
        ]);
    }
}
