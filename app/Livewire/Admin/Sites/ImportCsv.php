<?php

namespace App\Livewire\Admin\Sites;

use App\Models\Site;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImportCsv extends Component
{
    use WithFileUploads;

    public $file = null;
    public array $preview = [];
    public int $totalRows = 0;
    public int $successCount = 0;
    public int $errorCount = 0;
    public array $errors = [];
    public bool $imported = false;

    protected $listeners = ['resetImport' => 'resetImport'];

    public function resetImport(): void
    {
        $this->file = null;
        $this->preview = [];
        $this->totalRows = 0;
        $this->successCount = 0;
        $this->errorCount = 0;
        $this->errors = [];
        $this->imported = false;
    }

    public function updatedFile(): void
    {
        $this->preview = [];
        $this->totalRows = 0;
        $this->errors = [];
        $this->imported = false;

        if (!$this->file) return;

        $this->validate([
            'file' => 'required|mimes:csv,txt|max:10240',
        ]);

        $this->loadPreview();
    }

    private function loadPreview(): void
    {
        $handle = fopen($this->file->getPathname(), 'r');
        $header = fgetcsv($handle);

        if (!$header) {
            $this->errors[] = 'File CSV kosong atau format tidak valid.';
            return;
        }

        $normalizedHeader = array_map('strtolower', array_map('trim', $header));
        $requiredColumns = ['id_site', 'site'];
        $missingColumns = array_diff($requiredColumns, $normalizedHeader);

        if (!empty($missingColumns)) {
            $this->errors[] = 'Kolom wajib tidak ditemukan: ' . implode(', ', $missingColumns);
            return;
        }

        $rows = [];
        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < count($header)) continue;

            $data = array_combine($normalizedHeader, $row);
            $rows[] = $data;
            $count++;

            if ($count >= 10) break;
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
        $this->errors = [];

        $handle = fopen($this->file->getPathname(), 'r');
        $header = fgetcsv($handle);
        $normalizedHeader = array_map('strtolower', array_map('trim', $header));

        $rowNumber = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count($row) < count($header)) continue;

            $data = array_combine($normalizedHeader, $row);

            try {
                $idSite = trim($data['id_site'] ?? '');
                $siteName = trim($data['site'] ?? '');

                if (empty($idSite) || empty($siteName)) {
                    $this->errors[] = "Baris {$rowNumber}: id_site dan site wajib diisi.";
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

                $this->successCount++;
            } catch (\Exception $e) {
                $this->errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                $this->errorCount++;
            }
        }
        fclose($handle);

        $this->imported = true;
    }

    public function render()
    {
        return view('livewire.admin.sites.import-csv');
    }
}
