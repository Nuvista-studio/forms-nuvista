<?php

namespace App\Livewire\Admin\Assets;

use App\Helpers\ActivityLogger;
use App\Models\Asset;
use App\Models\Site;
use App\Models\User;
use Livewire\Component;

class CreateForm extends Component
{
    public string $kategori = '';
    public string $brand = '';
    public string $tipe = '';
    public string $namaPerangkat = '';
    public string $noSerial = '';
    public string $noAsset = '';
    public string $operatingUnit = '';
    public string $siteLocationAsset = '';
    public ?int $assignedUserId = null;

    protected function rules(): array
    {
        return [
            'kategori' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'tipe' => 'required|string|max:255',
            'namaPerangkat' => 'required|string|max:255',
            'noSerial' => 'nullable|string|max:255',
            'noAsset' => 'required|string|max:255|unique:assets,no_asset',
            'operatingUnit' => 'nullable|string|max:255',
            'siteLocationAsset' => 'nullable|string|max:255',
            'assignedUserId' => 'nullable|exists:users,id',
        ];
    }

    protected function messages(): array
    {
        return [
            'kategori.required' => 'Kategori wajib diisi.',
            'brand.required' => 'Brand wajib diisi.',
            'tipe.required' => 'Tipe wajib diisi.',
            'namaPerangkat.required' => 'Nama Perangkat wajib diisi.',
            'noAsset.required' => 'No Asset wajib diisi.',
            'noAsset.unique' => 'No Asset sudah terdaftar.',
            'assignedUserId.exists' => 'Pengguna tidak valid.',
        ];
    }

    public function save(): void
    {
        try {
            $this->validate();

            Asset::create([
                'kategori' => $this->kategori,
                'brand' => $this->brand,
                'tipe' => $this->tipe,
                'nama_perangkat' => $this->namaPerangkat,
                'no_serial' => $this->noSerial ?: null,
                'no_asset' => $this->noAsset,
                'status' => $this->assignedUserId ? 'active' : 'inactive',
                'operating_unit' => $this->operatingUnit ?: null,
                'site_location_asset' => $this->siteLocationAsset ?: null,
                'assigned_user_id' => $this->assignedUserId,
            ]);

            ActivityLogger::log('create', "Menambahkan asset baru: {$this->noAsset} - {$this->namaPerangkat}", 'App\Models\Asset', null, ['no_asset' => $this->noAsset]);
            $this->dispatch('asset-created');
            $this->reset();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('validation-error', errors: $e->errors());
        }
    }

    public function render()
    {
        return view('livewire.admin.assets.create-form', [
            'sites' => Site::orderBy('site')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }
}
