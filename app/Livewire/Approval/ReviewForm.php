<?php

namespace App\Livewire\Approval;

use App\Enums\ApprovalLevel;
use App\Enums\FormStatus;
use App\Models\FormApproval;
use App\Models\FormPemeriksaan;
use App\Models\FormPerawatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReviewForm extends Component
{
    public ?FormPemeriksaan $pemeriksaanForm = null;
    public ?FormPerawatan $perawatanForm = null;
    public string $formType = '';
    public ?int $formId = null;
    public ?FormApproval $currentApproval = null;
    public string $approvalLevel = '';
    public bool $canApprove = false;
    public string $catatan = '';
    public bool $saved = false;
    public bool $rejected = false;

    public string $rejectReason = '';
    public bool $showRejectModal = false;

    public function mount(string $type, string $id): void
    {
        $this->formType = $type;
        $this->formId = (int) $id;
        $user = Auth::user();

        if ($type === 'pemeriksaan') {
            $this->pemeriksaanForm = FormPemeriksaan::with(['teknisi', 'pengguna', 'asset', 'items', 'approvals'])
                ->findOrFail($this->formId);
        } elseif ($type === 'perawatan') {
            $this->perawatanForm = FormPerawatan::with(['teknisi', 'pengguna', 'asset', 'items', 'approvals'])
                ->findOrFail($this->formId);
        } else {
            abort(404);
        }

        $form = $this->getForm();
        $currentStatus = $form->status;

        if ($currentStatus === FormStatus::Selesai->value || $currentStatus === FormStatus::Draft->value) {
            abort(403, 'Form tidak tersedia untuk approval.');
        }

        $this->determineApprovalLevel($user, $form);
    }

    private function getForm(): FormPemeriksaan|FormPerawatan
    {
        return $this->formType === 'pemeriksaan' ? $this->pemeriksaanForm : $this->perawatanForm;
    }

    private function determineApprovalLevel($user, $form): void
    {
        if ($this->formType === 'pemeriksaan') {
            if ($user->hasPermissionTo('approve-diketahui') && $form->status === FormStatus::Diketahui->value) {
                $this->approvalLevel = ApprovalLevel::DiketahuiOleh->value;
                $this->canApprove = true;
            } elseif ($user->hasPermissionTo('approve-disetujui') && $form->status === FormStatus::Disetujui->value) {
                $this->approvalLevel = ApprovalLevel::DisetujuiOleh->value;
                $this->canApprove = true;
            }
        } else {
            if ($user->hasPermissionTo('approve-diketahui') && $form->status === FormStatus::Diketahui->value) {
                $this->approvalLevel = ApprovalLevel::DiketahuiOleh->value;
                $this->canApprove = true;
            } elseif ($user->hasPermissionTo('approve-disetujui') && $form->status === FormStatus::Disetujui->value) {
                $this->approvalLevel = ApprovalLevel::DisetujuiOleh->value;
                $this->canApprove = true;
            }
        }

        $this->currentApproval = $form->approvals()
            ->where('approval_level', $this->approvalLevel)
            ->first();
    }

    public function approveForm(string $signaturePath): void
    {
        if (!$this->canApprove) {
            $this->dispatch('error', message: 'Anda tidak memiliki akses untuk approve.');
            return;
        }

        $form = $this->getForm();

        DB::beginTransaction();

        try {
            $approval = $form->approvals()
                ->where('approval_level', $this->approvalLevel)
                ->first();

            if (!$approval) {
                $approval = FormApproval::create([
                    'approvable_type' => $this->formType === 'pemeriksaan' ? FormPemeriksaan::class : FormPerawatan::class,
                    'approvable_id' => $form->id,
                    'approval_level' => $this->approvalLevel,
                    'user_id' => Auth::id(),
                    'status' => 'pending',
                ]);
            }

            $approval->update([
                'status' => 'approved',
                'signature_path' => $signaturePath,
                'catatan' => $this->catatan ?: null,
                'approved_at' => now(),
            ]);

            $newStatus = match ($this->approvalLevel) {
                ApprovalLevel::DiketahuiOleh->value => FormStatus::Disetujui->value,
                ApprovalLevel::DisetujuiOleh->value => FormStatus::Selesai->value,
                default => $form->status,
            };

            $form->update(['status' => $newStatus]);

            if ($this->approvalLevel === ApprovalLevel::DiketahuiOleh->value) {
                $this->sendNextApprovalNotification($form);
            }

            DB::commit();

            $this->saved = true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', message: 'Gagal approve: ' . $e->getMessage());
        }
    }

    private function sendNextApprovalNotification($form): void
    {
        $managerIt = \App\Models\User::whereHas('roles', function ($q) {
            $q->where('name', 'manager_it');
        })->first();

        if ($managerIt) {
            $notifClass = \App\Notifications\ApprovalRequestNotification::class;
            $managerIt->notify(new $notifClass(
                formType: $this->formType,
                formId: $form->id,
                nomorForm: $form->nomor_form,
                approvalLevel: ApprovalLevel::DisetujuiOleh->value,
                submittedBy: $form->teknisi->name,
                deviceName: $form->asset->nama_perangkat,
            ));
        }
    }

    public function rejectForm(): void
    {
        if (!$this->canApprove) {
            $this->dispatch('error', message: 'Anda tidak memiliki akses untuk reject.');
            return;
        }

        if (empty($this->rejectReason)) {
            $this->dispatch('error', message: 'Alasan reject harus diisi.');
            return;
        }

        $form = $this->getForm();

        DB::beginTransaction();

        try {
            $approval = $form->approvals()
                ->where('approval_level', $this->approvalLevel)
                ->first();

            if ($approval) {
                $approval->update([
                    'status' => 'rejected',
                    'catatan' => $this->rejectReason,
                    'approved_at' => now(),
                ]);
            }

            $form->update(['status' => FormStatus::Revisi->value]);

            DB::commit();

            $this->showRejectModal = false;
            $this->rejected = true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', message: 'Gagal reject: ' . $e->getMessage());
        }
    }

    public function toggleRejectModal(): void
    {
        $this->showRejectModal = !$this->showRejectModal;
    }

    public function getStatusLabel(string $status): string
    {
        return match ($status) {
            'baik' => 'Baik',
            'tidak_baik' => 'Tidak Baik',
            'good_normal' => 'Good / Normal',
            'caution_poor' => 'Caution / Poor',
            'baru' => 'Baru',
            'lama' => 'Lama',
            default => $status,
        };
    }

    public function getStatusColor(string $status): string
    {
        return match ($status) {
            'baik', 'good_normal', 'baru' => 'text-emerald-400',
            'tidak_baik', 'caution_poor' => 'text-red-400',
            default => 'text-secondary',
        };
    }

    public function render()
    {
        return view('livewire.approval.review-form')->layout('components.app-layout');
    }
}
