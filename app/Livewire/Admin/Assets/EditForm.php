<?php

namespace App\Livewire\Admin\Assets;

use App\Models\Asset;
use App\Models\Site;
use Livewire\Component;

class EditForm extends Component
{
    public ?Asset $assetModel = null;
    public string $kategori = '';
    public string $brand = '';
    public string $tipe = '';
    public string $namaPerangkat = '';
    public string $noSerial = '';
    public string $noAsset = '';
    public string $status = 'active';
    public string $operatingUnit = '';
    public string $siteLocationAsset = '';

    public function mount(int $id): void
    {
        $asset = Asset::findOrFail($id);
        $this->assetModel = $asset;
        $this->kategori = $asset->kategori;
        $this->brand = $asset->brand;
        $this->tipe = $asset->tipe;
        $this->namaPerangkat = $asset->nama_perangkat;
        $this->noSerial = $asset->no_serial ?? '';
        $this->noAsset = $asset->no_asset;
        $this->status = $asset->status;
        $this->operatingUnit = $asset->operating_unit ?? '';
        $this->siteLocationAsset = $asset->site_location_asset ?? '';
    }

    protected function rules(): array
    {
        return [
            'kategori' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'tipe' => 'required|string|max:255',
            'namaPerangkat' => 'required|string|max:255',
            'noSerial' => 'nullable|string|max:255',
            'noAsset' => 'required|string|max:255|unique:assets,no_asset,' . $this->assetModel?->id,
            'status' => 'required|in:active,inactive,disposed',
            'operatingUnit' => 'nullable|string|max:255',
            'siteLocationAsset' => 'nullable|string|max:255',
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
            'status.required' => 'Status wajib dipilih.',
        ];
    }

    public function update(): void
    {
        try {
            $this->validate();

            $this->assetModel->update([
                'kategori' => $this->kategori,
                'brand' => $this->brand,
                'tipe' => $this->tipe,
                'nama_perangkat' => $this->namaPerangkat,
                'no_serial' => $this->noSerial ?: null,
                'no_asset' => $this->noAsset,
                'status' => $this->status,
                'operating_unit' => $this->operatingUnit ?: null,
                'site_location_asset' => $this->siteLocationAsset ?: null,
            ]);

            $this->dispatch('asset-updated');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('validation-error', errors: $e->errors());
        }
    }

    public function render()
    {
        return view('livewire.admin.assets.edit-form', [
            'sites' => Site::orderBy('site')->get(),
        ]);
    }
}
