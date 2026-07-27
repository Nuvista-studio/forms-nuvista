<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Rizky Pratama',
                'email' => 'admin@asri.co.id',
                'nik' => 'ADM001',
                'department' => 'IT Operation',
                'business_unit' => 'ASRI',
                'site' => 'Head Office',
                'role' => 'admin',
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'teknisi@asri.co.id',
                'nik' => 'IT001',
                'department' => 'IT Operation',
                'business_unit' => 'ASRI',
                'site' => 'Head Office',
                'role' => 'teknisi',
            ],
            [
                'name' => 'Dedi Kurniawan',
                'email' => 'teknisi2@asri.co.id',
                'nik' => 'IT002',
                'department' => 'IT Operation',
                'business_unit' => 'ASRI',
                'site' => 'Head Office',
                'role' => 'teknisi',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'user@asri.co.id',
                'nik' => 'USR001',
                'department' => 'Finance',
                'business_unit' => 'ASRI',
                'site' => 'Head Office',
                'role' => 'pengguna',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'user2@asri.co.id',
                'nik' => 'USR002',
                'department' => 'Marketing',
                'business_unit' => 'ASRI',
                'site' => 'Head Office',
                'role' => 'pengguna',
            ],
            [
                'name' => 'Maya Indah',
                'email' => 'user3@asri.co.id',
                'nik' => 'USR003',
                'department' => 'HRD',
                'business_unit' => 'ASRI',
                'site' => 'Head Office',
                'role' => 'pengguna',
            ],
            [
                'name' => 'Andi Wijaya',
                'email' => 'supervisor@asri.co.id',
                'nik' => 'SUP001',
                'department' => 'IT Operation',
                'business_unit' => 'ASRI',
                'site' => 'Head Office',
                'role' => 'supervisor_it',
            ],
            [
                'name' => 'Dewi Kartika',
                'email' => 'manager@asri.co.id',
                'nik' => 'MGR001',
                'department' => 'IT Operation',
                'business_unit' => 'ASRI',
                'site' => 'Head Office',
                'role' => 'manager_it',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password' => Hash::make('password'),
                    'theme_preference' => 'light',
                ])
            );

            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }
        }
    }
}
