<?php

namespace Database\Seeders;

use App\Models\ChecklistTemplate;
use Illuminate\Database\Seeder;

class ChecklistTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // ===== FORM PEMERIKSAAN =====

        // Hardware Pemeriksaan
        $hwPemeriksaan = ChecklistTemplate::create([
            'name' => 'Pemeriksaan Hardware',
            'form_type' => 'pemeriksaan',
            'category' => 'hardware',
            'is_active' => true,
        ]);

        $hwPemeriksaan->items()->createMany([
            ['name' => 'Processor', 'sort_order' => 1],
            ['name' => 'Mainboard', 'sort_order' => 2],
            ['name' => 'Monitor / LCD', 'sort_order' => 3],
            ['name' => 'Casing', 'sort_order' => 4],
            ['name' => 'Camera', 'sort_order' => 5],
            ['name' => 'Port USB / Charger', 'sort_order' => 6],
            ['name' => 'Connectivity (WiFi / Bluetooth / LAN)', 'sort_order' => 7],
            ['name' => 'Adaptor / PSU', 'sort_order' => 8],
            ['name' => 'Trackpad / Keyboard / Mouse', 'sort_order' => 9],
            ['name' => 'Battery', 'sort_order' => 10],
            ['name' => 'Audio', 'sort_order' => 11],
            ['name' => 'Memory (RAM)', 'sort_order' => 12],
            ['name' => 'Disk / Storage', 'sort_order' => 13],
            ['name' => 'Graphic Card', 'sort_order' => 14],
        ]);

        // Aplikasi Pemeriksaan
        $appPemeriksaan = ChecklistTemplate::create([
            'name' => 'Pemeriksaan Aplikasi',
            'form_type' => 'pemeriksaan',
            'category' => 'aplikasi',
            'is_active' => true,
        ]);

        $appPemeriksaan->items()->createMany([
            ['name' => 'Antivirus', 'sort_order' => 1],
            ['name' => 'Manage Engine', 'sort_order' => 2],
            ['name' => 'Office 365', 'sort_order' => 3],
            ['name' => 'OneDrive', 'sort_order' => 4],
            ['name' => 'Microsoft Teams', 'sort_order' => 5],
            ['name' => 'Adobe Reader', 'sort_order' => 6],
            ['name' => 'Browser', 'sort_order' => 7],
            ['name' => 'Anydesk', 'sort_order' => 8],
        ]);

        // OS Pemeriksaan
        $osPemeriksaan = ChecklistTemplate::create([
            'name' => 'Pemeriksaan Operating System',
            'form_type' => 'pemeriksaan',
            'category' => 'operating_system',
            'is_active' => true,
        ]);

        $osPemeriksaan->items()->createMany([
            ['name' => 'Nama OS', 'sort_order' => 1],
            ['name' => 'Nama Perangkat (Hostname)', 'sort_order' => 2],
            ['name' => 'User Account Profile', 'sort_order' => 3],
            ['name' => 'Disk Capacity Usage', 'sort_order' => 4],
            ['name' => 'Kinerja Sistem', 'sort_order' => 5],
        ]);

        // ===== FORM PERAWATAN =====

        // Hardware Perawatan
        $hwPerawatan = ChecklistTemplate::create([
            'name' => 'Perawatan Hardware',
            'form_type' => 'perawatan',
            'category' => 'hardware',
            'is_active' => true,
        ]);

        $hwPerawatan->items()->createMany([
            ['name' => 'Temperature Check', 'sort_order' => 1],
            ['name' => 'Physical Cleaning', 'sort_order' => 2],
            ['name' => 'Battery Report', 'sort_order' => 3],
            ['name' => 'Memory Test', 'sort_order' => 4],
            ['name' => 'Hardisk Test', 'sort_order' => 5],
        ]);

        // Aplikasi Perawatan
        $appPerawatan = ChecklistTemplate::create([
            'name' => 'Perawatan Aplikasi',
            'form_type' => 'perawatan',
            'category' => 'aplikasi',
            'is_active' => true,
        ]);

        $appPerawatan->items()->createMany([
            ['name' => 'Antivirus (Kaspersky)', 'sort_order' => 1],
            ['name' => 'Manage Device (Endpoint Central)', 'sort_order' => 2],
            ['name' => 'Office 365', 'sort_order' => 3],
            ['name' => 'Remote (Anydesk)', 'sort_order' => 4],
            ['name' => 'Browser (Edge/Chrome)', 'sort_order' => 5],
            ['name' => 'PDF (Adobe Acrobat/PDF SAM/PDF Gear)', 'sort_order' => 6],
            ['name' => 'Onedrive', 'sort_order' => 7],
            ['name' => 'Teams', 'sort_order' => 8],
            ['name' => 'File Extraction (7-Zip)', 'sort_order' => 9],
        ]);

        // OS Perawatan
        $osPerawatan = ChecklistTemplate::create([
            'name' => 'Perawatan Operating System',
            'form_type' => 'perawatan',
            'category' => 'operating_system',
            'is_active' => true,
        ]);

        $osPerawatan->items()->createMany([
            ['name' => 'Clear Cache', 'sort_order' => 1],
            ['name' => 'Defragment', 'sort_order' => 2],
            ['name' => 'Optimized RAM', 'sort_order' => 3],
            ['name' => 'Check Restore Point', 'sort_order' => 4],
            ['name' => 'Performance Check', 'sort_order' => 5],
        ]);
    }
}
