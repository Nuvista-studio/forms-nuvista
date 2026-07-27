<?php

namespace Database\Seeders;

use App\Enums\FormStatus;
use App\Models\Asset;
use App\Models\ChecklistTemplate;
use App\Models\FormPemeriksaan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FormPemeriksaanSeeder extends Seeder
{
    public function run(): void
    {
        $teknisiUsers = User::whereHas('role', fn($q) => $q->where('name', 'teknisi'))->get();
        $penggunaUsers = User::whereHas('role', fn($q) => $q->where('name', 'pengguna'))->get();
        $assets = Asset::whereNotNull('assigned_user_id')->get();

        if ($teknisiUsers->isEmpty() || $penggunaUsers->isEmpty() || $assets->isEmpty()) {
            return;
        }

        $hardwareItems = ChecklistTemplate::where('form_type', 'pemeriksaan')->where('category', 'hardware')->first()?->items ?? collect();
        $aplikasiItems = ChecklistTemplate::where('form_type', 'pemeriksaan')->where('category', 'aplikasi')->first()?->items ?? collect();
        $osItems = ChecklistTemplate::where('form_type', 'pemeriksaan')->where('category', 'operating_system')->first()?->items ?? collect();

        $statuses = [
            'draft' => 2,
            'submitted' => 2,
            'diketahui' => 2,
            'disetujui' => 2,
            'selesai' => 2,
        ];

        $formNumber = 1;
        $now = Carbon::now();

        foreach ($statuses as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                $teknisi = $teknisiUsers->random();
                $pengguna = $penggunaUsers->random();
                $asset = $assets->random();

                $submittedAt = match ($status) {
                    'draft' => null,
                    default => $now->copy()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                };

                $form = FormPemeriksaan::create([
                    'nomor_form' => 'PBR-' . str_pad($formNumber++, 4, '0', STR_PAD_LEFT),
                    'user_id' => $teknisi->id,
                    'pengguna_id' => $pengguna->id,
                    'asset_id' => $asset->id,
                    'site_location' => $asset->operating_unit,
                    'location_detail' => 'Lantai ' . rand(1, 5),
                    'kondisi' => rand(0, 1) ? 'baru' : 'lama',
                    'notes' => 'Pemeriksaan rutin untuk ' . $asset->nama_perangkat,
                    'status' => $status,
                    'submitted_at' => $submittedAt,
                ]);

                $this->createItems($form, $hardwareItems, 'hardware');
                $this->createItems($form, $aplikasiItems, 'aplikasi');
                $this->createOsItems($form, $osItems);
            }
        }
    }

    private function createItems($form, $items, string $category): void
    {
        foreach ($items as $idx => $item) {
            $form->items()->create([
                'template_item_id' => $item->id,
                'category' => $category,
                'name' => $item->name,
                'status' => rand(0, 1) ? 'baik' : 'tidak_baik',
                'keterangan' => null,
                'sort_order' => $idx,
            ]);
        }
    }

    private function createOsItems($form, $items): void
    {
        foreach ($items as $idx => $item) {
            $value = match (true) {
                str_contains(strtolower($item->name), 'os') => 'Windows 11 Pro',
                str_contains(strtolower($item->name), 'hostname') => 'PC-' . strtoupper(uniqid()),
                str_contains(strtolower($item->name), 'user') => $form->teknisi->name ?? 'User',
                str_contains(strtolower($item->name), 'disk') => rand(40, 80) . '% used',
                str_contains(strtolower($item->name), 'kinerja') => null,
                default => null,
            };

            $form->items()->create([
                'template_item_id' => $item->id,
                'category' => 'operating_system',
                'name' => $item->name,
                'status' => $value ? null : (rand(0, 1) ? 'baik' : 'tidak_baik'),
                'value' => $value,
                'keterangan' => null,
                'sort_order' => $idx,
            ]);
        }
    }
}
