<?php

namespace App\Livewire\Admin\Sites;

use App\Helpers\ActivityLogger;
use App\Models\Site;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showDeleteModal = false;
    public ?string $deleteSiteId = null;
    public string $deleteSiteName = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(string $idSite, string $name): void
    {
        $this->deleteSiteId = $idSite;
        $this->deleteSiteName = $name;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteSiteId = null;
        $this->deleteSiteName = '';
    }

    public function deleteSite(): void
    {
        Site::where('id_site', $this->deleteSiteId)->delete();

        ActivityLogger::log('delete', "Menghapus site: {$this->deleteSiteId} - {$this->deleteSiteName}", 'App\Models\Site', $this->deleteSiteId);
        $this->cancelDelete();
        $this->dispatch('site-deleted');
    }

    public function render()
    {
        $query = Site::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('id_site', 'like', "%{$this->search}%")
                    ->orWhere('site', 'like', "%{$this->search}%")
                    ->orWhere('provincy', 'like', "%{$this->search}%")
                    ->orWhere('city', 'like', "%{$this->search}%");
            }))
            ->orderBy('id_site');

        $sites = $query->paginate(15);

        return view('livewire.admin.sites.index', [
            'sites' => $sites,
        ]);
    }
}
