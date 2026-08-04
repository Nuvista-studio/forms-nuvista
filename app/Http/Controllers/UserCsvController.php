<?php

namespace App\Http\Controllers;

use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserCsvController extends Controller
{
    public function export(): StreamedResponse
    {
        $users = User::with('roles')->orderBy('name')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users_export_' . now()->format('Y-m-d_His') . '.csv"',
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['name', 'email', 'password', 'nik', 'role', 'status']);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    '',
                    $user->nik ?? '',
                    $user->getRoleNames()->first() ?? '',
                    $user->status ?? 'Enable',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function template(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_users.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['name', 'email', 'password', 'nik', 'role', 'status']);

            fputcsv($file, [
                'John Doe',
                'john@asri.co.id',
                'password',
                'USR001',
                'pengguna',
                'Enable',
            ]);

            fputcsv($file, [
                'Jane Smith',
                'jane@asri.co.id',
                'password123',
                'USR002',
                'teknisi',
                'Enable',
            ]);

            fputcsv($file, [
                'Budi Santoso',
                'budi@asri.co.id',
                'passbudi',
                'USR003',
                'admin',
                'Disable',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
