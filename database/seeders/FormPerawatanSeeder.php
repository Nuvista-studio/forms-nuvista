<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\ChecklistTemplate;
use App\Models\FormPerawatan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FormPerawatanSeeder extends Seeder
{
    public function run(): void
    {
        $teknisiUsers = User::whereHas('role', fn($q) => $q->where('name', 'teknisi'))->get();
        $penggunaUsers = User::whereHas('role', fn($q) => $q->where('name', 'pengguna'))->get();
        $assets = Asset::whereNotNull('assigned_user_id')->get();

        if ($teknisiUsers->isEmpty() || $penggunaUsers->isEmpty() || $assets->isEmpty()) {
            return;
        }

        $hardwareItems = ChecklistTemplate::where('form_type', 'perawatan')->where('category', 'hardware')->first()?->items ?? collect();
        $aplikasiItems = ChecklistTemplate::where('form_type', 'perawatan')->where('category', 'aplikasi')->first()?->items ?? collect();
        $osItems = ChecklistTemplate::where('form_type', 'perawatan')->where('category', 'operating_system')->first()?->items ?? collect();

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

                $form = FormPerawatan::create([
                    'nomor_form' => 'PRW-' . str_pad($formNumber++, 4, '0', STR_PAD_LEFT),
                    'user_id' => $teknisi->id,
                    'pengguna_id' => $pengguna->id,
                    'asset_id' => $asset->id,
                    'site_location' => $asset->operating_unit,
                    'location_detail' => 'Lantai ' . rand(1, 5),
                    'kondisi_akhir' => ['good', 'fair', 'critical', 'poor'][rand(0, 3)],
                    'notes' => 'Perawatan rutin untuk ' . $asset->nama_perangkat,
                    'status' => $status,
                    'submitted_at' => $submittedAt,
                ]);

                $this->createItems($form, $hardwareItems, 'hardware');
                $this->createItems($form, $aplikasiItems, 'aplikasi');
                $this->createItems($form, $osItems, 'operating_system');
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
}
