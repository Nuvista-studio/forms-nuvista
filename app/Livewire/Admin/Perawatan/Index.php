<?php

namespace App\Livewire\Admin\Perawatan;

use App\Models\FormPerawatan;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $kondisi_akhir = '';

    public ?array $viewingForm = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'kondisi_akhir' => ['except' => ''],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedKondisiAkhir(): void
    {
        $this->resetPage();
    }

    public function viewForm(int $id): void
    {
        $form = FormPerawatan::with(['teknisi', 'pengguna', 'asset', 'site', 'items', 'approvals.user', 'attachments'])
            ->findOrFail($id);

        $this->viewingForm = [
            'id' => $form->id,
            'nomor_form' => $form->nomor_form,
            'status' => $form->status,
            'submitted_at' => $form->submitted_at?->format('d/m/Y H:i'),
            'kondisi_akhir' => $form->kondisi_akhir,
            'kondisi_akhir_notes' => $form->kondisi_akhir_notes,
            'notes' => $form->notes,
            'location_detail' => $form->location_detail,
            'barcode_fisik' => (bool) ($form->barcode_fisik ?? false),
            'teknisi' => $form->teknisi ? ['name' => $form->teknisi->name, 'email' => $form->teknisi->email] : null,
            'pengguna' => $form->pengguna ? ['name' => $form->pengguna->name, 'nik' => $form->pengguna->nik, 'department' => $form->pengguna->department] : null,
            'asset' => $form->asset ? [
                'nama_perangkat' => $form->asset->nama_perangkat,
                'no_asset' => $form->asset->no_asset,
                'kategori' => $form->asset->kategori,
                'brand' => $form->asset->brand,
                'tipe' => $form->asset->tipe,
                'no_serial' => $form->asset->no_serial,
            ] : null,
            'site' => $form->site ? ['site' => $form->site->site] : null,
            'site_location' => $form->site_location,
            'items' => $form->items->map(fn ($item) => [
                'name' => $item->name,
                'category' => $item->category,
                'status' => $item->status,
                'keterangan' => $item->keterangan,
            ])->toArray(),
            'approvals' => $form->approvals->map(fn ($a) => [
                'approval_level' => $a->approval_level,
                'status' => $a->status,
                'user_name' => $a->user?->name ?? $a->custom_signer_name,
                'approved_at' => $a->approved_at?->format('d/m/Y H:i'),
            ])->toArray(),
        ];
    }

    public function closeView(): void
    {
        $this->viewingForm = null;
    }

    public function render()
    {
        $query = FormPerawatan::with(['teknisi', 'pengguna', 'asset', 'site'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('nomor_form', 'like', "%{$this->search}%")
                    ->orWhereHas('teknisi', fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('pengguna', fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('asset', fn ($q) => $q->where('nama_perangkat', 'like', "%{$this->search}%")
                        ->orWhere('no_asset', 'like', "%{$this->search}%"));
            }))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->kondisi_akhir, fn ($q) => $q->where('kondisi_akhir', $this->kondisi_akhir))
            ->latest('submitted_at');

        $forms = $query->paginate(15);

        return view('livewire.admin.perawatan.index', [
            'forms' => $forms,
        ]);
    }
}
