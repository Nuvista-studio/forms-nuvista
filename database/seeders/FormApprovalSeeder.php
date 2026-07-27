<?php

namespace Database\Seeders;

use App\Enums\ApprovalLevel;
use App\Models\FormApproval;
use App\Models\FormPemeriksaan;
use App\Models\FormPerawatan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FormApprovalSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPemeriksaanApprovals();
        $this->seedPerawatanApprovals();
    }

    private function seedPemeriksaanApprovals(): void
    {
        $forms = FormPemeriksaan::with(['teknisi', 'pengguna'])->get();

        foreach ($forms as $form) {
            $submittedAt = $form->submitted_at;
            if (!$submittedAt) continue;

            $supervisor = User::whereHas('role', fn($q) => $q->where('name', 'supervisor_it'))->first();
            $manager = User::whereHas('role', fn($q) => $q->where('name', 'manager_it'))->first();

            // Diperiksa oleh (teknisi) - always approved when form is submitted
            if (in_array($form->status, ['submitted', 'diketahui', 'disetujui', 'selesai'])) {
                $form->approvals()->create([
                    'approval_level' => ApprovalLevel::DiperiksaOleh->value,
                    'user_id' => $form->teknisi->id,
                    'status' => 'approved',
                    'approved_at' => $submittedAt->copy()->addMinutes(rand(5, 30)),
                    'catatan' => 'Pemeriksaan selesai',
                ]);
            }

            // Diketahui oleh (pengguna)
            if (in_array($form->status, ['diketahui', 'disetujui', 'selesai'])) {
                $form->approvals()->create([
                    'approval_level' => ApprovalLevel::DiketahuiOleh->value,
                    'user_id' => $form->pengguna_id,
                    'status' => 'approved',
                    'approved_at' => $submittedAt->copy()->addHours(rand(1, 24)),
                    'catatan' => 'Diketahui, sesuai kondisi',
                ]);
            }

            if ($form->status === 'diketahui') {
                $form->approvals()->create([
                    'approval_level' => ApprovalLevel::DisetujuiOleh->value,
                    'user_id' => $supervisor?->id,
                    'status' => 'pending',
                ]);
            }

            // Disetujui oleh (supervisor/manager)
            if (in_array($form->status, ['disetujui', 'selesai'])) {
                $approver = $manager ?? $supervisor;
                $form->approvals()->create([
                    'approval_level' => ApprovalLevel::DisetujuiOleh->value,
                    'user_id' => $approver?->id,
                    'status' => 'approved',
                    'approved_at' => $submittedAt->copy()->addDays(rand(1, 3)),
                    'catatan' => 'Disetujui untuk diproses',
                ]);
            }
        }
    }

    private function seedPerawatanApprovals(): void
    {
        $forms = FormPerawatan::with(['teknisi', 'pengguna'])->get();

        foreach ($forms as $form) {
            $submittedAt = $form->submitted_at;
            if (!$submittedAt) continue;

            $supervisor = User::whereHas('role', fn($q) => $q->where('name', 'supervisor_it'))->first();
            $manager = User::whereHas('role', fn($q) => $q->where('name', 'manager_it'))->first();

            // Diperiksa oleh (teknisi)
            if (in_array($form->status, ['submitted', 'diketahui', 'disetujui', 'selesai'])) {
                $form->approvals()->create([
                    'approval_level' => ApprovalLevel::DiperiksaOleh->value,
                    'user_id' => $form->teknisi->id,
                    'status' => 'approved',
                    'approved_at' => $submittedAt->copy()->addMinutes(rand(5, 30)),
                    'catatan' => 'Perawatan selesai',
                ]);
            }

            // Diketahui oleh (pengguna)
            if (in_array($form->status, ['diketahui', 'disetujui', 'selesai'])) {
                $form->approvals()->create([
                    'approval_level' => ApprovalLevel::DiketahuiOleh->value,
                    'user_id' => $form->pengguna_id,
                    'status' => 'approved',
                    'approved_at' => $submittedAt->copy()->addHours(rand(1, 24)),
                    'catatan' => 'Diketahui, perawatan sudah sesuai',
                ]);
            }

            if ($form->status === 'diketahui') {
                $form->approvals()->create([
                    'approval_level' => ApprovalLevel::DisetujuiOleh->value,
                    'user_id' => $supervisor?->id,
                    'status' => 'pending',
                ]);
            }

            // Disetujui oleh (supervisor/manager)
            if (in_array($form->status, ['disetujui', 'selesai'])) {
                $approver = $manager ?? $supervisor;
                $form->approvals()->create([
                    'approval_level' => ApprovalLevel::DisetujuiOleh->value,
                    'user_id' => $approver?->id,
                    'status' => 'approved',
                    'approved_at' => $submittedAt->copy()->addDays(rand(1, 3)),
                    'catatan' => 'Disetujui',
                ]);
            }
        }
    }
}
