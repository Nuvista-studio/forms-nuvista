<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            ChecklistTemplateSeeder::class,
            AssetSeeder::class,
            SiteSeeder::class,
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Rizky Pratama',
            'email' => 'admin@asri.co.id',
            'password' => Hash::make('password'),
            'nik' => 'ADM001',
            'department' => 'IT Operation',
            'business_unit' => 'ASRI',
            'site' => 'Head Office',
        ]);
        $admin->assignRole('admin');

        // Create sample teknisi
        $teknisi = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'teknisi@asri.co.id',
            'password' => Hash::make('password'),
            'nik' => 'IT001',
            'department' => 'IT Operation',
            'business_unit' => 'ASRI',
            'site' => 'Head Office',
        ]);
        $teknisi->assignRole('teknisi');

        $teknisi2 = User::create([
            'name' => 'Dedi Kurniawan',
            'email' => 'teknisi2@asri.co.id',
            'password' => Hash::make('password'),
            'nik' => 'IT002',
            'department' => 'IT Operation',
            'business_unit' => 'ASRI',
            'site' => 'Head Office',
        ]);
        $teknisi2->assignRole('teknisi');

        // Create sample pengguna
        $pengguna = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'user@asri.co.id',
            'password' => Hash::make('password'),
            'nik' => 'USR001',
            'department' => 'Finance',
            'business_unit' => 'ASRI',
            'site' => 'Head Office',
        ]);
        $pengguna->assignRole('pengguna');

        $pengguna2 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'user2@asri.co.id',
            'password' => Hash::make('password'),
            'nik' => 'USR002',
            'department' => 'Marketing',
            'business_unit' => 'ASRI',
            'site' => 'Head Office',
        ]);
        $pengguna2->assignRole('pengguna');

        $pengguna3 = User::create([
            'name' => 'Maya Indah',
            'email' => 'user3@asri.co.id',
            'password' => Hash::make('password'),
            'nik' => 'USR003',
            'department' => 'HRD',
            'business_unit' => 'ASRI',
            'site' => 'Head Office',
        ]);
        $pengguna3->assignRole('pengguna');

        // Create sample supervisor
        $supervisor = User::create([
            'name' => 'Andi Wijaya',
            'email' => 'supervisor@asri.co.id',
            'password' => Hash::make('password'),
            'nik' => 'SUP001',
            'department' => 'IT Operation',
            'business_unit' => 'ASRI',
            'site' => 'Head Office',
        ]);
        $supervisor->assignRole('supervisor_it');

        // Create sample manager
        $manager = User::create([
            'name' => 'Dewi Kartika',
            'email' => 'manager@asri.co.id',
            'password' => Hash::make('password'),
            'nik' => 'MGR001',
            'department' => 'IT Operation',
            'business_unit' => 'ASRI',
            'site' => 'Head Office',
        ]);
        $manager->assignRole('manager_it');
    }
}
