<?php

use App\Enums\ApprovalLevel;
use App\Enums\FormStatus;
use App\Models\FormApproval;
use App\Models\FormPerawatan;
use App\Models\User;
use App\Notifications\ApprovalRequestNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.app-layout')] class extends Component
{
    public ?FormPerawatan $form = null;
    public string $catatan = '';
    public bool $saved = false;

    public function mount(string $id): void
    {
        $this->form = FormPerawatan::with(['teknisi', 'pengguna', 'asset', 'items', 'approvals'])
            ->findOrFail($id);
    }

    public function approve(string $signaturePath): void
    {
        $approval = $this->form->approvals()
            ->where('approval_level', ApprovalLevel::DiperiksaOleh)
            ->first();

        if (!$approval) {
            $approval = FormApproval::create([
                'approvable_type' => FormPerawatan::class,
                'approvable_id' => $this->form->id,
                'approval_level' => ApprovalLevel::DiperiksaOleh,
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

        $this->form->update(['status' => FormStatus::Diketahui->value]);

        $this->sendDiketahuiNotification();

        $this->saved = true;
    }

    private function sendDiketahuiNotification(): void
    {
        $supervisorIt = User::whereHas('roles', function ($q) {
            $q->where('name', 'supervisor_it');
        })->first();

        if ($supervisorIt) {
            $supervisorIt->notify(new ApprovalRequestNotification(
                formType: 'perawatan',
                formId: $this->form->id,
                nomorForm: $this->form->nomor_form,
                approvalLevel: ApprovalLevel::DiketahuiOleh->value,
                submittedBy: $this->form->teknisi->name,
                deviceName: $this->form->asset->nama_perangkat,
            ));
        }
    }

    public function render(): mixed
    {
        return view('livewire.pages.perawatan.signature');
    }
}; ?>

<div class="max-w-4xl mx-auto px-4 py-6">
    @if($saved)
        <div class="glass-card p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-emerald-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h2 class="text-xl font-bold text-primary mb-2">Tanda Tangan Berhasil Disimpan</h2>
            <p class="text-sm text-muted mb-6">Form {{ $form->nomor_form }} telah ditandatangani sebagai "Perawatan Oleh"</p>
            <a href="{{ route('dashboard') }}" wire:navigate class="glass-button-primary inline-block">Kembali ke Dashboard</a>
        </div>
    @else
        <h1 class="text-2xl font-bold text-primary mb-6">Tanda Tangan - Perawatan Oleh</h1>

        <div class="glass-card p-4 mb-4">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                <div><span class="text-xs text-muted">No. Form</span><p class="font-mono font-semibold text-primary">{{ $form->nomor_form }}</p></div>
                <div><span class="text-xs text-muted">Teknisi</span><p class="text-primary">{{ $form->teknisi->name }}</p></div>
                <div><span class="text-xs text-muted">Perangkat</span><p class="text-primary">{{ $form->asset->nama_perangkat }}</p></div>
                <div><span class="text-xs text-muted">No. Asset</span><p class="font-mono text-primary">{{ $form->asset->no_asset }}</p></div>
            </div>
        </div>

        <div class="glass-card p-4 mb-4">
            <h3 class="text-sm font-semibold text-primary mb-3">Catatan (opsional)</h3>
            <textarea wire:model.live="catatan" rows="2" class="glass-input w-full rounded-lg px-3 py-2 text-sm resize-none" placeholder="Tambahkan catatan..."></textarea>
        </div>

        <div class="glass-card p-4 mb-4">
            <h3 class="text-sm font-semibold text-primary mb-3">Tanda Tangan</h3>
            <div x-data="{
                canvas: null, ctx: null, drawing: false, lastX: 0, lastY: 0,
                init() { this.canvas = this.$refs.signatureCanvas; this.ctx = this.canvas.getContext('2d'); this.resize(); window.addEventListener('resize', () => this.resize()); },
                resize() { const rect = this.canvas.parentElement.getBoundingClientRect(); this.canvas.width = rect.width; this.canvas.height = 200; this.ctx.strokeStyle = getComputedStyle(document.documentElement).getPropertyValue('--color-text-primary').trim(); this.ctx.lineWidth = 2; this.ctx.lineCap = 'round'; this.ctx.lineJoin = 'round'; },
                startDraw(e) { this.drawing = true; const rect = this.canvas.getBoundingClientRect(); const touch = e.touches ? e.touches[0] : e; this.lastX = touch.clientX - rect.left; this.lastY = touch.clientY - rect.top; },
                draw(e) { if (!this.drawing) return; const rect = this.canvas.getBoundingClientRect(); const touch = e.touches ? e.touches[0] : e; const x = touch.clientX - rect.left; const y = touch.clientY - rect.top; this.ctx.beginPath(); this.ctx.moveTo(this.lastX, this.lastY); this.ctx.lineTo(x, y); this.ctx.stroke(); this.lastX = x; this.lastY = y; },
                stopDraw() { this.drawing = false; },
                clear() { this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height); },
                isEmpty() { const pixel = this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height).data; return !pixel.some(v => v !== 0); },
                save() { if (this.isEmpty()) { alert('Harap tanda tangan terlebih dahulu'); return; } $wire.approve(this.canvas.toDataURL('image/png')); }
            }" class="space-y-3">
                <div class="rounded-lg overflow-hidden border-2" style="border-color: var(--color-border);">
                    <canvas x-ref="signatureCanvas" class="w-full cursor-crosshair touch-none" style="background: var(--color-bg-secondary); height: 200px;"
                        @mousedown="startDraw($event)" @mousemove="draw($event)" @mouseup="stopDraw()" @mouseleave="stopDraw()"
                        @touchstart.prevent="startDraw($event)" @touchmove.prevent="draw($event)" @touchend="stopDraw()"></canvas>
                </div>
                <div class="flex gap-2">
                    <button @click="clear()" type="button" class="glass-button-secondary text-sm flex-1">Hapus</button>
                    <button @click="save()" type="button" class="glass-button-primary text-sm flex-1">Simpan Tanda Tangan</button>
                </div>
            </div>
        </div>
    @endif
</div>
