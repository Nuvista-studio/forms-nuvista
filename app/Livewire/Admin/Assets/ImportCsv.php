<?php

namespace App\Livewire\Admin\Assets;

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
        $this->imported = false;
    }

    public function updatedFile(): void
    {
        $this->preview = [];
        $this->totalRows = 0;
        $this->importErrors = [];
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
            $this->importErrors[] = 'File CSV kosong atau format tidak valid.';
            return;
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
        $this->importErrors = [];

        $handle = fopen($this->file->getPathname(), 'r');
        $header = fgetcsv($handle);
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

                $assignedUserId = null;
                $assignedEmail = trim($data['assigned_user_email'] ?? '');
                if (!empty($assignedEmail)) {
                    $user = User::where('email', $assignedEmail)->first();
                    $assignedUserId = $user?->id;
                }

                Asset::updateOrCreate(
                    ['no_asset' => $noAsset],
                    [
                        'kategori' => $kategori,
                        'brand' => $brand,
                        'tipe' => trim($data['tipe'] ?? '') ?: '',
                        'nama_perangkat' => trim($data['nama_perangkat'] ?? '') ?: $noAsset,
                        'no_serial' => trim($data['no_serial'] ?? '') ?: null,
                        'qr_code' => $noAsset,
                        'operating_unit' => trim($data['operating_unit'] ?? '') ?: null,
                        'site_location_asset' => trim($data['site_location_asset'] ?? '') ?: null,
                        'assigned_user_id' => $assignedUserId,
                        'status' => $assignedUserId ? 'active' : 'inactive',
                    ]
                );

                $this->successCount++;
            } catch (\Exception $e) {
                $this->importErrors[] = "Baris {$rowNumber}: " . $e->getMessage();
                $this->errorCount++;
            }
        }
        fclose($handle);

        $this->imported = true;
    }

    public function render()
    {
        return view('livewire.admin.assets.import-csv');
    }
}
