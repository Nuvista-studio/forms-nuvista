<?php

namespace App\Livewire\Admin\Assets;

use App\Models\Asset;
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
            ]);

            $this->dispatch('asset-updated');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('validation-error', errors: $e->errors());
        }
    }

    public function render()
    {
        return view('livewire.admin.assets.edit-form');
    }
}
