<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
        $requiredColumns = ['name', 'email'];
        $missingColumns = array_diff($requiredColumns, $normalizedHeader);

        if (!empty($missingColumns)) {
            $this->importErrors[] = 'Kolom wajib tidak ditemukan: ' . implode(', ', $missingColumns);
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

        // Count total rows
        $handle = fopen($this->file->getPathname(), 'r');
        fgetcsv($handle); // skip header
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

            $data = array_combine($normalizedHeader, $row);

            try {
                $name = trim($data['name'] ?? '');
                $email = trim($data['email'] ?? '');

                if (empty($name) || empty($email)) {
                    $this->importErrors[] = "Baris {$rowNumber}: Nama dan email wajib diisi.";
                    $this->errorCount++;
                    continue;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->importErrors[] = "Baris {$rowNumber}: Format email tidak valid ({$email}).";
                    $this->errorCount++;
                    continue;
                }

                if (User::where('email', $email)->exists()) {
                    $this->importErrors[] = "Baris {$rowNumber}: Email sudah terdaftar ({$email}).";
                    $this->errorCount++;
                    continue;
                }

                $password = trim($data['password'] ?? '');
                if (empty($password)) {
                    $password = 'password';
                }

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'nik' => trim($data['nik'] ?? '') ?: null,
                    'department' => trim($data['department'] ?? '') ?: null,
                    'business_unit' => trim($data['business_unit'] ?? '') ?: null,
                    'site' => trim($data['site'] ?? '') ?: null,
                    'no_telepon' => trim($data['no_telepon'] ?? '') ?: null,
                ]);

                $role = trim($data['role'] ?? '');
                if (!empty($role) && \Spatie\Permission\Models\Role::where('name', $role)->exists()) {
                    $user->assignRole($role);
                } else {
                    $user->assignRole('pengguna');
                }

                $this->successCount++;
            } catch (\Exception $e) {
                $this->importErrors[] = "Baris {$rowNumber}: " . $e->getMessage();
                $this->errorCount++;
            }
        }
        fclose($handle);

        $this->imported = true;
    }

    public function getRoleList(): array
    {
        return \Spatie\Permission\Models\Role::pluck('name')->toArray();
    }

    public function render()
    {
        return view('livewire.users.import-csv');
    }
}
