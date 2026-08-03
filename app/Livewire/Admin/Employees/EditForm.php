<?php

namespace App\Livewire\Admin\Employees;

use App\Helpers\ActivityLogger;
use App\Models\Employee;
use App\Models\Site;
use App\Models\User;
use Livewire\Component;

class EditForm extends Component
{
    public ?Employee $employee = null;
    public string $name = '';
    public string $nik = '';
    public string $department = '';
    public string $business_unit = '';
    public string $site = '';
    public string $no_telepon = '';
    public string $email = '';
    public string $status = Employee::STATUS_ACTIVE;
    public ?int $linkedUserId = null;

    public array $assignedAssets = [];

    public function mount(int $id): void
    {
        $employee = Employee::with('user')->findOrFail($id);
        $this->employee = $employee;
        $this->name = $employee->name ?? '';
        $this->nik = $employee->nik ?? '';
        $this->department = $employee->department ?? '';
        $this->business_unit = $employee->business_unit ?? '';
        $this->site = $employee->site ?? '';
        $this->no_telepon = $employee->no_telepon ?? '';
        $this->email = $employee->email ?? '';
        $this->status = $employee->status ?? Employee::STATUS_ACTIVE;
        $this->linkedUserId = $employee->user?->id;
        $this->assignedAssets = $employee->assignedAssets()
            ->get(['id', 'no_asset', 'nama_perangkat', 'brand', 'tipe', 'no_serial'])
            ->toArray();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:50|unique:employees,nik,'.$this->employee->id,
            'department' => 'nullable|string|max:255',
            'business_unit' => 'nullable|string|max:50|exists:sites,id_corp',
            'site' => 'nullable|string|max:50|exists:sites,id_site',
            'no_telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255|unique:employees,email,'.$this->employee->id,
            'status' => 'nullable|in:active,resigned',
            'linkedUserId' => 'nullable|exists:users,id',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'nik.unique' => 'NIK sudah terdaftar pada employee lain.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar pada employee lain.',
            'status.in' => 'Status harus Active atau Resigned.',
            'linkedUserId.exists' => 'Akun login tidak valid.',
        ];
    }

    public function update(): void
    {
        try {
            $this->validate();

            if ($this->status === Employee::STATUS_RESIGNED && ! empty($this->assignedAssets)) {
                $this->addError(
                    'status',
                    'Employee masih memiliki ' . count($this->assignedAssets) . ' asset terpasang. Kembalikan asset terlebih dahulu melalui Form Pengembalian Asset.'
                );
                $this->dispatch('show-toast', message: 'Data gagal disimpan. Employee masih memiliki asset terpasang.', type: 'error');

                return;
            }

            $this->employee->update([
                'name' => $this->name,
                'nik' => $this->nik ?: null,
                'department' => $this->department ?: null,
                'business_unit' => $this->business_unit ?: null,
                'site' => $this->site ?: null,
                'no_telepon' => $this->no_telepon ?: null,
                'email' => $this->email ?: null,
                'status' => $this->status,
            ]);

            $this->syncLinkedUser();

            ActivityLogger::log('update', "Mengubah employee: {$this->name}", 'App\Models\Employee', $this->employee->id);
            session()->flash('success', 'Employee berhasil diperbarui.');
            $this->redirect(route('admin.employees.index'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('show-toast', message: 'Data gagal disimpan. Periksa kembali isian form, termasuk NIK/Email yang sudah terdaftar.', type: 'error');
            $this->dispatch('validation-error', errors: $e->errors());
        }
    }

    private function syncLinkedUser(): void
    {
        $currentUserId = $this->employee->user?->id;

        if ($currentUserId === $this->linkedUserId) {
            return;
        }

        if ($currentUserId) {
            User::where('id', $currentUserId)->update(['employee_id' => null]);
        }

        if ($this->linkedUserId) {
            User::where('id', $this->linkedUserId)->update(['employee_id' => $this->employee->id]);
        }

        $this->employee->load('user');
    }

    public function unlinkUser(): void
    {
        $this->linkedUserId = null;
    }

    public function getSiteList(): array
    {
        return Site::orderBy('id_site')->get(['id_site', 'site'])
            ->mapWithKeys(fn ($s) => [$s->id_site => "{$s->id_site} - {$s->site}"])
            ->toArray();
    }

    public function getBusinessUnitList(): array
    {
        return Site::select('id_corp')->distinct()->orderBy('id_corp')->pluck('id_corp')
            ->mapWithKeys(fn ($code) => [$code => $code])
            ->toArray();
    }

    public function getLinkableUsers(): array
    {
        return User::whereNull('employee_id')
            ->orWhere('employee_id', $this->employee->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.employees.edit-form', [
            'linkableUsers' => $this->getLinkableUsers(),
        ]);
    }
}
