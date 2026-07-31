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

            fputcsv($file, ['name', 'email', 'password', 'nik', 'department', 'business_unit', 'site', 'no_telepon', 'role']);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    '',
                    $user->nik ?? '',
                    $user->department ?? '',
                    $user->business_unit ?? '',
                    $user->site ?? '',
                    $user->no_telepon ?? '',
                    $user->getRoleNames()->first() ?? '',
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

            fputcsv($file, ['name', 'email', 'password', 'nik', 'department', 'business_unit', 'site', 'no_telepon', 'role']);

            fputcsv($file, [
                'John Doe',
                'john@asri.co.id',
                'password',
                'USR001',
                'IT Operation',
                'MAS',
                'O99',
                '081234567890',
                'pengguna',
            ]);

            fputcsv($file, [
                'Jane Smith',
                'jane@asri.co.id',
                'password123',
                'USR002',
                'Finance',
                'MAP',
                'M01',
                '081234567891',
                'teknisi',
            ]);

            fputcsv($file, [
                'Budi Santoso',
                'budi@asri.co.id',
                'passbudi',
                'USR003',
                'HRD',
                'PPP',
                'A03',
                '081234567892',
                'admin',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
