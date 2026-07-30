<?php

namespace App\Livewire\Admin\Assets;

use App\Helpers\ActivityLogger;
use App\Models\Asset;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showDeleteModal = false;

    public ?int $deleteAssetId = null;

    public string $deleteAssetName = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatedSearch(): void
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
            }))
            ->orderBy('no_asset');

        $assets = $query->paginate(15);

        return view('livewire.admin.assets.index', [
            'assets' => $assets,
        ]);
    }
}
