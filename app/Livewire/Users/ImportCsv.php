<?php

namespace App\Livewire\Users;

use App\Helpers\ActivityLogger;
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

            $data = array_combine($normalizedHeader, array_slice($row, 0, count($normalizedHeader)));
            $rows[] = $data;
            $count++;

            if ($count >= 5) break;
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

                $site = trim($data['site'] ?? '');
                if (!empty($site) && !\App\Models\Site::where('id_site', $site)->exists()) {
                    $this->importErrors[] = "Baris {$rowNumber}: Site tidak valid ({$site}). Gunakan kode id_site (contoh: O99).";
                    $this->errorCount++;
                    continue;
                }

                $businessUnit = trim($data['business_unit'] ?? '');
                if (!empty($businessUnit) && !\App\Models\Site::where('id_corp', $businessUnit)->exists()) {
                    $this->importErrors[] = "Baris {$rowNumber}: Business unit tidak valid ({$businessUnit}). Gunakan kode id_corp (contoh: MAS).";
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

        if ($this->errorCount > 0) {
            $this->dispatch('show-toast', message: "Import selesai: {$this->successCount} berhasil, {$this->errorCount} gagal. Lihat detail error di bawah.", type: 'error');
        } else {
            $this->dispatch('show-toast', message: "Import selesai: {$this->successCount} data berhasil diimpor.", type: 'success');
        }

        ActivityLogger::log('import', "Mengimpor {$this->successCount} data user" . ($this->errorCount ? " ({$this->errorCount} gagal)" : ''));
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
