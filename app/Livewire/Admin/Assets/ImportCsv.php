<?php

namespace App\Livewire\Admin\Assets;

use App\Helpers\ActivityLogger;
use App\Models\Asset;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImportCsv extends Component
{
    use WithFileUploads;

    public $file;
    public array $preview = [];
    public int $totalRows = 0;
    public int $successCount = 0;
    public int $errorCount = 0;
    public array $importErrors = [];
    public array $importSuccess = [];
    public string $resultTab = 'gagal';
    public array $validRows = [];
    public bool $processed = false;
    public bool $imported = false;

    protected $listeners = ['resetImport' => 'resetImport'];

    public function resetImport(): void
    {
        $this->file = null;
        $this->preview = [];
        $this->totalRows = 0;
        $this->successCount = 0;
        $this->errorCount = 0;
        $this->importErrors = [];
        $this->importSuccess = [];
        $this->resultTab = 'gagal';
        $this->validRows = [];
        $this->processed = false;
        $this->imported = false;
        $this->resetValidation();
    }

    public function updatedFile(): void
    {
        $this->preview = [];
        $this->totalRows = 0;
        $this->importErrors = [];
        $this->importSuccess = [];
        $this->validRows = [];
        $this->processed = false;
        $this->imported = false;

        if (!$this->file) return;

        try {
            $this->validate(
                ['file' => 'required|mimes:csv,txt|max:10240'],
                [
                    'file.required' => 'Pilih file CSV terlebih dahulu',
                    'file.mimes' => 'File harus berformat .csv atau .txt',
                    'file.max' => 'Ukuran file melebihi batas maksimal (10MB)',
                ]
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->addError('file', $e->validator->errors()->first('file'));
            $this->file = null;
            $this->dispatch('show-toast', message: 'Upload CSV gagal: ' . $e->validator->errors()->first('file'), type: 'error');
            return;
        }

        $this->loadPreview();

        if (!empty($this->importErrors)) {
            $this->dispatch('show-toast', message: 'Data CSV tidak sesuai: ' . $this->importErrors[0], type: 'error');
        }
    }

    private function loadPreview(): void
    {
        $handle = fopen($this->file->getPathname(), 'r');
        $header = fgetcsv($handle);

        if (!$header) {
            $this->importErrors[] = 'File CSV kosong atau format tidak valid.';
            return;
        }

        if (isset($header[0])) {
            $header[0] = ltrim($header[0], "\xEF\xBB\xBF");
        }

        $normalizedHeader = array_map('strtolower', array_map('trim', $header));
        $requiredColumns = ['no_asset', 'kategori', 'brand'];
        $missingColumns = array_diff($requiredColumns, $normalizedHeader);

        if (!empty($missingColumns)) {
            $this->importErrors[] = 'Kolom wajib tidak ditemukan: ' . implode(', ', $missingColumns);
            return;
        }

        $rows = [];
        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < count($header)) continue;

            $data = array_combine($normalizedHeader, array_slice($row, 0, count($normalizedHeader)));
            $rows[] = $data;
            $count++;

            if ($count >= 5) break;
        }
        fclose($handle);

        $handle = fopen($this->file->getPathname(), 'r');
        fgetcsv($handle);
        $this->totalRows = 0;
        while (fgetcsv($handle) !== false) {
            $this->totalRows++;
        }
        fclose($handle);

        $this->preview = $rows;
    }

    public function processData(): void
    {
        if (!$this->file) return;

        set_time_limit(0);

        $this->successCount = 0;
        $this->errorCount = 0;
        $this->importErrors = [];
        $this->importSuccess = [];
        $this->validRows = [];
        $this->resultTab = 'gagal';

        $handle = fopen($this->file->getPathname(), 'r');
        $header = fgetcsv($handle);

        if (isset($header[0])) {
            $header[0] = ltrim($header[0], "\xEF\xBB\xBF");
        }

        $normalizedHeader = array_map('strtolower', array_map('trim', $header));

        $rowNumber = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count($row) < count($header)) continue;

            $data = array_combine($normalizedHeader, array_slice($row, 0, count($normalizedHeader)));

            try {
                $noAsset = trim($data['no_asset'] ?? '');
                $kategori = trim($data['kategori'] ?? '');
                $brand = trim($data['brand'] ?? '');

                if (empty($noAsset) || empty($kategori) || empty($brand)) {
                    $this->importErrors[] = "Baris {$rowNumber}: no_asset, kategori, dan brand wajib diisi.";
                    $this->errorCount++;
                    continue;
                }

                $assignedEmail = trim($data['assigned_user_email'] ?? '');

                $this->validRows[] = [
                    'row' => $rowNumber,
                    'data' => [
                        'no_asset' => $noAsset,
                        'kategori' => $kategori,
                        'brand' => $brand,
                        'tipe' => trim($data['tipe'] ?? '') ?: '',
                        'nama_perangkat' => trim($data['nama_perangkat'] ?? '') ?: $noAsset,
                        'no_serial' => trim($data['no_serial'] ?? '') ?: null,
                        'operating_unit' => trim($data['operating_unit'] ?? '') ?: null,
                        'site_location_asset' => trim($data['site_location_asset'] ?? '') ?: null,
                        'assigned_user_email' => $assignedEmail,
                    ],
                ];

                $this->importSuccess[] = [
                    'row' => $rowNumber,
                    'data' => [
                        'no_asset' => $noAsset,
                        'kategori' => $kategori,
                        'brand' => $brand,
                        'tipe' => trim($data['tipe'] ?? '') ?: '-',
                        'nama_perangkat' => trim($data['nama_perangkat'] ?? '') ?: $noAsset,
                        'no_serial' => trim($data['no_serial'] ?? '') ?: '-',
                        'operating_unit' => trim($data['operating_unit'] ?? '') ?: '-',
                        'site_location_asset' => trim($data['site_location_asset'] ?? '') ?: '-',
                        'assigned_user_email' => $assignedEmail ?: '-',
                    ],
                ];

                $this->successCount++;
            } catch (\Exception $e) {
                $this->importErrors[] = "Baris {$rowNumber}: " . $e->getMessage();
                $this->errorCount++;
            }
        }
        fclose($handle);

        $this->processed = true;

        if ($this->errorCount > 0) {
            $this->dispatch('show-toast', message: "Data terbaca: {$this->successCount} berhasil, {$this->errorCount} gagal. Periksa detail sebelum mengirim.", type: 'error');
        } else {
            $this->dispatch('show-toast', message: "Data terbaca: {$this->successCount} data valid. Klik 'Konfirmasi Kirim Data' untuk menyimpan.", type: 'success');
        }
    }

    public function confirmImport(): void
    {
        if (!$this->processed || $this->imported) return;

        set_time_limit(0);

        $importedCount = 0;

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            foreach ($this->validRows as $validRow) {
                $data = $validRow['data'];

                $assignedUserId = null;
                if (!empty($data['assigned_user_email'])) {
                    $user = User::where('email', $data['assigned_user_email'])->first();
                    $assignedUserId = $user?->id;
                }

                Asset::updateOrCreate(
                    ['no_asset' => $data['no_asset']],
                    [
                        'kategori' => $data['kategori'],
                        'brand' => $data['brand'],
                        'tipe' => $data['tipe'],
                        'nama_perangkat' => $data['nama_perangkat'],
                        'no_serial' => $data['no_serial'],
                        'qr_code' => $data['no_asset'],
                        'operating_unit' => $data['operating_unit'],
                        'site_location_asset' => $data['site_location_asset'],
                        'assigned_user_id' => $assignedUserId,
                        'status' => $assignedUserId ? 'active' : 'inactive',
                    ]
                );

                $importedCount++;
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Throwable $e) {
            if (\Illuminate\Support\Facades\DB::transactionLevel() > 0) {
                \Illuminate\Support\Facades\DB::rollBack();
            }
            $this->dispatch('show-toast', message: 'Kirim data gagal (perubahan dibatalkan): ' . $e->getMessage(), type: 'error');

            return;
        }

        $this->imported = true;

        if ($this->errorCount > 0) {
            $this->dispatch('show-toast', message: "Import selesai: {$importedCount} berhasil, {$this->errorCount} gagal. Lihat detail error di bawah.", type: 'error');
        } else {
            $this->dispatch('show-toast', message: "Import selesai: {$importedCount} data berhasil diimpor.", type: 'success');
        }

        ActivityLogger::log('import', "Mengimpor {$importedCount} data asset" . ($this->errorCount ? " ({$this->errorCount} gagal)" : ''));
    }

    public function render()
    {
        return view('livewire.admin.assets.import-csv');
    }
}
