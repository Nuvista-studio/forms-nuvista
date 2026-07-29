<?php

namespace App\Livewire\Admin\Backup;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class Index extends Component
{
    public bool $isCreating = false;
    public ?string $errorMessage = null;
    public ?string $successMessage = null;

    public function createBackup(): void
    {
        $this->isCreating = true;
        $this->errorMessage = null;
        $this->successMessage = null;

        try {
            $mysqldump = $this->findMysqldump();
            if (!$mysqldump) {
                throw new \Exception('mysqldump tidak ditemukan di sistem. Pastikan MySQL terinstall.');
            }

            $dir = storage_path('app/backups');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $timestamp = now()->format('Y-m-d_H-i-s');
            $sqlFile = "{$dir}/backup_{$timestamp}.sql";
            $zipPath = "{$dir}/backup_{$timestamp}.zip";

            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            $passwordArg = $password ? ' --password=' . escapeshellarg($password) : '';
            $cmd = sprintf(
                '%s --host=%s --port=%s --user=%s%s %s --routines --single-transaction --quick > %s 2>&1',
                escapeshellarg($mysqldump),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                $passwordArg,
                escapeshellarg($database),
                escapeshellarg($sqlFile)
            );

            exec($cmd, $output, $exitCode);

            if (!file_exists($sqlFile) || filesize($sqlFile) === 0) {
                $error = implode("\n", $output);
                @unlink($sqlFile);
                throw new \Exception("Gagal membuat database dump" . ($error ? ": {$error}" : ''));
            }

            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
                throw new \Exception('Gagal membuat file arsip zip');
            }

            $zip->addFile($sqlFile, 'database/' . basename($sqlFile));

            $storagePath = storage_path('app/public');
            if (is_dir($storagePath)) {
                $this->addToZip($zip, $storagePath, 'storage');
            }

            $zip->close();

            unlink($sqlFile);

            $this->successMessage = "Backup berhasil dibuat: backup_{$timestamp}.zip";
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->isCreating = false;
    }

    public function deleteBackup(string $filename): void
    {
        $path = storage_path('app/backups/' . basename($filename));
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function getBackupsProperty(): array
    {
        $dir = storage_path('app/backups');
        if (!is_dir($dir)) {
            return [];
        }

        $files = glob($dir . '/*.zip');
        $backups = [];
        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'size' => filesize($file),
                'size_formatted' => $this->formatSize(filesize($file)),
                'created_at' => date('Y-m-d H:i:s', filemtime($file)),
            ];
        }

        usort($backups, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return $backups;
    }

    private function findMysqldump(): ?string
    {
        $candidates = [
            'mysqldump',
            '/Applications/XAMPP/bin/mysqldump',
            '/Applications/MAMP/Library/bin/mysqldump',
            '/usr/local/mysql/bin/mysqldump',
            '/opt/homebrew/bin/mysqldump',
            '/usr/bin/mysqldump',
        ];

        foreach ($candidates as $cmd) {
            $output = null;
            $code = null;
            exec("which " . escapeshellarg($cmd) . " 2>/dev/null", $output, $code);
            if ($code === 0 && !empty($output[0])) {
                return $output[0];
            }
            if (file_exists($cmd)) {
                return $cmd;
            }
        }

        return null;
    }

    private function addToZip(ZipArchive $zip, string $path, string $parent): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        $baseLength = strlen($path) + 1;
        foreach ($files as $file) {
            if (!$file->isFile()) continue;
            $realPath = $file->getRealPath();
            $relativePath = $parent . '/' . substr($realPath, $baseLength);
            $zip->addFile($realPath, $relativePath);
        }
    }

    private function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function render()
    {
        return view('livewire.admin.backup.index');
    }
}
