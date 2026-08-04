<?php

namespace App\Livewire\Admin\Employees;

use App\Helpers\ActivityLogger;
use App\Models\Employee;
use App\Models\Site;
use Livewire\Component;

class CreateForm extends Component
{
    public string $name = '';
    public string $nik = '';
    public string $site = '';
    public string $no_telepon = '';
    public string $email = '';
    public string $status = Employee::STATUS_ACTIVE;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:50|unique:employees,nik',
            'site' => 'nullable|string|max:50|exists:sites,id_site',
            'no_telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255|unique:employees,email|exists:users,email',
            'status' => 'nullable|in:Active,Resigned',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'nik.unique' => 'NIK sudah terdaftar pada employee lain.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar pada employee lain.',
            'email.exists' => 'Email harus terdaftar sebagai akun user terlebih dahulu.',
            'status.in' => 'Status harus Active atau Resigned.',
        ];
    }

    public function save(): void
    {
        try {
            $this->validate();

            $employee = Employee::create([
                'name' => $this->name,
                'nik' => $this->nik ?: null,
                'site' => $this->site ?: null,
                'no_telepon' => $this->no_telepon ?: null,
                'email' => $this->email ?: null,
                'status' => $this->status,
                'akun_login' => $this->email ? 'Connect' : 'No Access',
            ]);

            if ($this->email) {
                $user = \App\Models\User::find($this->email);
                if ($user && $user->status === \App\Models\User::STATUS_RESIGNED) {
                    $employee->update(['akun_login' => 'No Access']);
                }
            }

            ActivityLogger::log('create', "Menambahkan employee baru: {$this->name}", 'App\Models\Employee', $employee->nik);
            session()->flash('success', 'Employee berhasil ditambahkan.');
            $this->redirect(route('admin.employees.index'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('show-toast', message: 'Data gagal disimpan. Periksa kembali isian form, termasuk NIK/Email yang sudah terdaftar.', type: 'error');
            $this->dispatch('validation-error', errors: $e->errors());
        }
    }

    public function getSiteList(): array
    {
        return Site::orderBy('id_site')->get(['id_site', 'site'])
            ->mapWithKeys(fn ($s) => [$s->id_site => "{$s->id_site} - {$s->site}"])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.employees.create-form');
    }
}
