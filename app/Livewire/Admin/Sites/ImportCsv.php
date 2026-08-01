<?php

namespace App\Livewire\Admin\Sites;

use App\Helpers\ActivityLogger;
use App\Models\Site;
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
        $this->imported = false;
        $this->resetValidation();
    }

    public function updatedFile(): void
    {
        $this->preview = [];
        $this->totalRows = 0;
        $this->importErrors = [];
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
        $requiredColumns = ['id_site', 'site'];
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

    public function import(): void
    {
        if (!$this->file) return;

        $this->successCount = 0;
        $this->errorCount = 0;
        $this->importErrors = [];
        $this->importSuccess = [];
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

            if (count($row) > count($normalizedHeader)) {
                $this->importErrors[] = "Baris {$rowNumber}: jumlah kolom (" . count($row) . ") tidak sesuai header (" . count($header) . "), baris dilewati.";
                $this->errorCount++;
                continue;
            }

            $data = array_combine($normalizedHeader, $row);

            try {
                $idSite = trim($data['id_site'] ?? '');
                $siteName = trim($data['site'] ?? '');

                if (empty($idSite) || empty($siteName)) {
                    $this->importErrors[] = "Baris {$rowNumber}: id_site dan site wajib diisi.";
                    $this->errorCount++;
                    continue;
                }

                Site::updateOrCreate(
                    ['id_site' => $idSite],
                    [
                        'site' => $siteName,
                        'buss' => trim($data['buss'] ?? '') ?: null,
                        'id_corp' => trim($data['id_corp'] ?? '') ?: null,
                        'country' => trim($data['country'] ?? '') ?: null,
                        'provincy' => trim($data['provincy'] ?? '') ?: null,
                        'city' => trim($data['city'] ?? '') ?: null,
                        'address' => trim($data['address'] ?? '') ?: null,
                        'url_maps' => trim($data['url_maps'] ?? '') ?: null,
                    ]
                );

                $this->importSuccess[] = [
                    'row' => $rowNumber,
                    'data' => [
                        'id_site' => $idSite,
                        'site' => $siteName,
                        'buss' => trim($data['buss'] ?? '') ?: '-',
                        'id_corp' => trim($data['id_corp'] ?? '') ?: '-',
                        'country' => trim($data['country'] ?? '') ?: '-',
                        'provincy' => trim($data['provincy'] ?? '') ?: '-',
                        'city' => trim($data['city'] ?? '') ?: '-',
                        'address' => trim($data['address'] ?? '') ?: '-',
                        'url_maps' => trim($data['url_maps'] ?? '') ?: '-',
                    ],
                ];

                $this->successCount++;
            } catch (\Exception $e) {
                $this->importErrors[] = "Baris {$rowNumber}: " . $e->getMessage();
                $this->errorCount++;
            }
        }
        fclose($handle);

        $this->imported = true;

        if ($this->errorCount > 0) {
            $this->dispatch('show-toast', message: "Import selesai: {$this->successCount} berhasil, {$this->errorCount} gagal. Lihat detail error di bawah.", type: 'error');
        } else {
            $this->dispatch('show-toast', message: "Import selesai: {$this->successCount} data berhasil diimpor.", type: 'success');
        }

        ActivityLogger::log('import', "Mengimpor {$this->successCount} data site" . ($this->errorCount ? " ({$this->errorCount} gagal)" : ''));
    }

    public function render()
    {
        return view('livewire.admin.sites.import-csv');
    }
}
