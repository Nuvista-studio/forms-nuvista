<?php

namespace App\Livewire\Perawatan;

use App\Enums\FormStatus;
use App\Enums\KondisiPerawatan;
use App\Models\Asset;
use App\Models\ChecklistTemplate;
use App\Models\FormApproval;
use App\Models\FormPerawatan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateForm extends Component
{
    use WithFileUploads;

    public int $currentStep = 1;
    public const TOTAL_STEPS = 8;

    public array $stepTitles = [
        1 => 'Info Pengguna',
        2 => 'Info Perangkat',
        3 => 'Perawatan Hardware',
        4 => 'Perawatan Aplikasi',
        5 => 'Perawatan Operating System',
        6 => 'Kondisi Setelah Perawatan',
        7 => 'Catatan Tambahan',
        8 => 'Review & Submit',
    ];

    // Step 1: Info Pengguna
    public ?int $penggunaId = null;
    public string $teknisiName = '';
    public string $teknisiNik = '';
    public string $teknisiDepartment = '';
    public string $teknisiBusinessUnit = '';
    public string $teknisiSite = '';
    public string $penggunaName = '';
    public string $penggunaNik = '';
    public string $penggunaDepartment = '';
    public string $penggunaEmail = '';

    // Step 2: Info Perangkat
    public ?int $assetId = null;
    public string $scanResult = '';
    public string $kategori = '';
    public string $brand = '';
    public string $tipe = '';
    public string $namaPerangkat = '';
    public string $noSerial = '';
    public string $noAsset = '';

    // Steps 3-5: Checklist items
    public array $hardwareItems = [];
    public array $aplikasiItems = [];
    public array $osItems = [];

    // Step 6: Kondisi Setelah Perawatan
    public string $kondisiAkhir = '';
    public string $kondisiAkhirNotes = '';

    // Step 7: Catatan
    public string $notes = '';

    // Draft
    public ?int $formId = null;
    public bool $isDraft = false;
    public string $nomorForm = '';

    // Search
    public string $penggunaSearch = '';
    public array $penggunaResults = [];
    public bool $showPenggunaDropdown = false;

    // Photo uploads
    public array $itemPhotos = [];

    protected $listeners = [
        'scanCompleted' => 'handleScan',
        'autosave' => 'saveDraft',
    ];

    protected function rules(): array
    {
        return [
            'penggunaId' => 'required|exists:users,id',
            'assetId' => 'required|exists:assets,id',
            'hardwareItems.*.status' => 'nullable|in:baik,tidak_baik',
            'hardwareItems.*.keterangan' => 'nullable|string|max:1000',
            'aplikasiItems.*.status' => 'nullable|in:baik,tidak_baik',
            'aplikasiItems.*.keterangan' => 'nullable|string|max:1000',
            'osItems.*.status' => 'nullable|in:baik,tidak_baik',
            'osItems.*.keterangan' => 'nullable|string|max:1000',
            'kondisiAkhir' => 'required|in:good_normal,caution_poor',
            'kondisiAkhirNotes' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function mount(?int $formId = null): void
    {
        $user = Auth::user();
        $this->teknisiName = $user->name;
        $this->teknisiNik = $user->nik ?? '';
        $this->teknisiDepartment = $user->department ?? '';
        $this->teknisiBusinessUnit = $user->business_unit ?? '';
        $this->teknisiSite = $user->site ?? '';

        if (request('formId')) {
            $formId = request('formId');
        }

        $this->loadChecklistTemplates();

        if ($formId) {
            $this->loadFormData($formId);
        }
    }

    private function loadFormData(int $formId): void
    {
        $form = FormPerawatan::with(['items', 'pengguna', 'asset'])->find($formId);
        if (!$form || ($form->status !== 'draft' && $form->status !== 'revisi')) return;

        $this->formId = $form->id;
        $this->nomorForm = $form->nomor_form;
        $this->isDraft = true;

        $this->penggunaId = $form->pengguna_id;
        if ($form->pengguna) {
            $this->penggunaName = $form->pengguna->name;
            $this->penggunaNik = $form->pengguna->nik ?? '';
            $this->penggunaDepartment = $form->pengguna->department ?? '';
            $this->penggunaEmail = $form->pengguna->email;
        }

        $this->assetId = $form->asset_id;
        if ($form->asset) {
            $this->kategori = $form->asset->kategori ?? '';
            $this->brand = $form->asset->brand ?? '';
            $this->tipe = $form->asset->tipe ?? '';
            $this->namaPerangkat = $form->asset->nama_perangkat ?? '';
            $this->noSerial = $form->asset->no_serial ?? '';
            $this->noAsset = $form->asset->no_asset ?? '';
        }

        $this->kondisiAkhir = $form->kondisi_akhir ?? '';
        $this->kondisiAkhirNotes = $form->kondisi_akhir_notes ?? '';
        $this->notes = $form->notes ?? '';

        foreach ($form->items as $item) {
            $category = $item->category;
            if ($category === 'hardware' && isset($this->hardwareItems[$item->sort_order])) {
                $this->hardwareItems[$item->sort_order]['status'] = $item->status;
                $this->hardwareItems[$item->sort_order]['keterangan'] = $item->keterangan ?? '';
            } elseif ($category === 'aplikasi' && isset($this->aplikasiItems[$item->sort_order])) {
                $this->aplikasiItems[$item->sort_order]['status'] = $item->status;
                $this->aplikasiItems[$item->sort_order]['keterangan'] = $item->keterangan ?? '';
            } elseif ($category === 'operating_system' && isset($this->osItems[$item->sort_order])) {
                $this->osItems[$item->sort_order]['status'] = $item->status;
                $this->osItems[$item->sort_order]['keterangan'] = $item->keterangan ?? '';
            }
        }
    }

    private function loadChecklistTemplates(): void
    {
        $hwTemplate = ChecklistTemplate::where('form_type', 'perawatan')
            ->where('category', 'hardware')
            ->where('is_active', true)
            ->with('items')
            ->first();

        if ($hwTemplate) {
            $this->hardwareItems = $hwTemplate->items->sortBy('sort_order')->map(fn($item) => [
                'template_item_id' => $item->id,
                'name' => $item->name,
                'status' => null,
                'keterangan' => '',
                'sort_order' => $item->sort_order,
            ])->values()->toArray();
        }

        $appTemplate = ChecklistTemplate::where('form_type', 'perawatan')
            ->where('category', 'aplikasi')
            ->where('is_active', true)
            ->with('items')
            ->first();

        if ($appTemplate) {
            $this->aplikasiItems = $appTemplate->items->sortBy('sort_order')->map(fn($item) => [
                'template_item_id' => $item->id,
                'name' => $item->name,
                'status' => null,
                'keterangan' => '',
                'sort_order' => $item->sort_order,
            ])->values()->toArray();
        }

        $osTemplate = ChecklistTemplate::where('form_type', 'perawatan')
            ->where('category', 'operating_system')
            ->where('is_active', true)
            ->with('items')
            ->first();

        if ($osTemplate) {
            $this->osItems = $osTemplate->items->sortBy('sort_order')->map(fn($item) => [
                'template_item_id' => $item->id,
                'name' => $item->name,
                'status' => null,
                'keterangan' => '',
                'sort_order' => $item->sort_order,
            ])->values()->toArray();
        }
    }

    public function searchPengguna(): void
    {
        if (strlen($this->penggunaSearch) < 2) {
            $this->penggunaResults = [];
            $this->showPenggunaDropdown = false;
            return;
        }

        $this->penggunaResults = User::where('name', 'like', "%{$this->penggunaSearch}%")
            ->orWhere('nik', 'like', "%{$this->penggunaSearch}%")
            ->orWhere('email', 'like', "%{$this->penggunaSearch}%")
            ->limit(10)
            ->get()
            ->toArray();

        $this->showPenggunaDropdown = count($this->penggunaResults) > 0;
    }

    public function selectPengguna(int $userId): void
    {
        $user = User::find($userId);
        if ($user) {
            $this->penggunaId = $userId;
            $this->penggunaName = $user->name;
            $this->penggunaNik = $user->nik ?? '';
            $this->penggunaDepartment = $user->department ?? '';
            $this->penggunaEmail = $user->email;
            $this->penggunaSearch = $user->name;
            $this->showPenggunaDropdown = false;
        }
    }

    public function clearPengguna(): void
    {
        $this->penggunaId = null;
        $this->penggunaName = '';
        $this->penggunaNik = '';
        $this->penggunaDepartment = '';
        $this->penggunaEmail = '';
        $this->penggunaSearch = '';
    }

    public function handleScan(string $code): void
    {
        $this->scanResult = $code;

        $asset = Asset::where('qr_code', $code)
            ->orWhere('no_asset', $code)
            ->orWhere('no_serial', $code)
            ->first();

        if ($asset) {
            $this->assetId = $asset->id;
            $this->kategori = $asset->kategori;
            $this->brand = $asset->brand;
            $this->tipe = $asset->tipe;
            $this->namaPerangkat = $asset->nama_perangkat;
            $this->noSerial = $asset->no_serial ?? '';
            $this->noAsset = $asset->no_asset;

            $this->dispatch('assetFound', asset: [
                'id' => $asset->id,
                'no_asset' => $asset->no_asset,
                'nama_perangkat' => $asset->nama_perangkat,
            ]);
        } else {
            $this->dispatch('assetNotFound', code: $code);
            $this->resetAssetFields();
        }
    }

    public function searchAssetManual(): void
    {
        if (empty($this->noAsset)) {
            $this->resetAssetFields();
            return;
        }

        $asset = Asset::where('no_asset', $this->noAsset)->first();

        if ($asset) {
            $this->assetId = $asset->id;
            $this->kategori = $asset->kategori;
            $this->brand = $asset->brand;
            $this->tipe = $asset->tipe;
            $this->namaPerangkat = $asset->nama_perangkat;
            $this->noSerial = $asset->no_serial ?? '';
            $this->noAsset = $asset->no_asset;

            $this->dispatch('assetFound', asset: [
                'id' => $asset->id,
                'no_asset' => $asset->no_asset,
                'nama_perangkat' => $asset->nama_perangkat,
            ]);
        }
    }

    private function resetAssetFields(): void
    {
        $this->assetId = null;
        $this->kategori = '';
        $this->brand = '';
        $this->tipe = '';
        $this->namaPerangkat = '';
        $this->noSerial = '';
    }

    public function generateNomorForm(): string
    {
        $today = now()->format('dmY');
        $prefix = '001/PWT';

        $count = FormPerawatan::where('nomor_form', 'like', "%{$prefix}/%/{$today}")
            ->count();

        $sequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        $assetCode = $this->noAsset ?? 'XXXX';

        return "{$sequence}/PWT/{$assetCode}/{$today}";
    }

    public function nextStep(): void
    {
        if ($this->currentStep < self::TOTAL_STEPS) {
            $this->currentStep++;
        }
    }

    public function prevStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= self::TOTAL_STEPS) {
            $this->currentStep = $this->currentStep === $step ? 0 : $step;
        }
    }

    public function toggleItemStatus(string $list, int $index, string $status): void
    {
        if (!isset($this->$list[$index])) return;

        $current = $this->$list[$index]['status'] ?? null;
        $this->$list[$index]['status'] = $current === $status ? null : $status;
    }

    public function saveDraft(): void
    {
        $data = $this->getFormData();
        $data['status'] = FormStatus::Draft->value;

        if ($this->formId) {
            $form = FormPerawatan::find($this->formId);
            if ($form) {
                $form->update($data);
                $this->syncItems($form);
                return;
            }
        }

        if (!$this->nomorForm) {
            $this->nomorForm = $this->generateNomorForm();
        }

        $data['nomor_form'] = $this->nomorForm;
        $form = FormPerawatan::create($data);
        $this->formId = $form->id;
        $this->isDraft = true;

        $this->syncItems($form);

        $this->dispatch('draftSaved');
    }

    private function getFormData(): array
    {
        return [
            'user_id' => Auth::id(),
            'pengguna_id' => $this->penggunaId,
            'asset_id' => $this->assetId,
            'kondisi_akhir' => $this->kondisiAkhir ?: null,
            'kondisi_akhir_notes' => $this->kondisiAkhirNotes ?: null,
            'notes' => $this->notes ?: null,
        ];
    }

    private function syncItems(FormPerawatan $form): void
    {
        $allItems = array_merge(
            array_map(fn($i) => array_merge($i, ['category' => 'hardware']), $this->hardwareItems),
            array_map(fn($i) => array_merge($i, ['category' => 'aplikasi']), $this->aplikasiItems),
            array_map(fn($i) => array_merge($i, ['category' => 'operating_system']), $this->osItems),
        );

        foreach ($allItems as $item) {
            $form->items()->updateOrCreate(
                [
                    'template_item_id' => $item['template_item_id'],
                ],
                [
                    'category' => $item['category'],
                    'name' => $item['name'],
                    'status' => $item['status'] ?: null,
                    'keterangan' => $item['keterangan'] ?? null,
                    'sort_order' => $item['sort_order'] ?? 0,
                ]
            );
        }
    }

    public function submitForm(): void
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->first();
            $this->dispatch('submitError', message: $firstError ?? 'Mohon lengkapi semua field yang wajib diisi');
            return;
        }

        DB::beginTransaction();

        try {
            $nomorForm = $this->formId
                ? FormPerawatan::find($this->formId)->nomor_form
                : $this->generateNomorForm();

            $data = $this->getFormData();
            $data['nomor_form'] = $nomorForm;
            $data['status'] = FormStatus::Submitted->value;
            $data['submitted_at'] = now();

            if ($this->formId) {
                $form = FormPerawatan::findOrFail($this->formId);
                $form->update($data);
            } else {
                $form = FormPerawatan::create($data);
                $this->formId = $form->id;
            }

            $this->syncItems($form);

            FormApproval::create([
                'approvable_type' => FormPerawatan::class,
                'approvable_id' => $form->id,
                'approval_level' => 'diperiksa_oleh',
                'user_id' => Auth::id(),
                'status' => 'pending',
            ]);

            DB::commit();

            $this->dispatch('formSubmitted', formId: $form->id);

            $this->redirect(route('perawatan.signature', $form->id), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('submitError', message: $e->getMessage());
        }
    }

    public function getFormNumberPreview(): string
    {
        if ($this->noAsset) {
            $today = now()->format('dmY');
            $count = FormPerawatan::where('nomor_form', 'like', "%/PWT/{$this->noAsset}/{$today}")->count();
            $seq = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            return "{$seq}/PWT/{$this->noAsset}/{$today}";
        }
        return '---/PWT/XXXX/' . now()->format('dmY');
    }

    public function render()
    {
        return view('livewire.perawatan.create-form')->layout('components.app-layout');
    }
}
