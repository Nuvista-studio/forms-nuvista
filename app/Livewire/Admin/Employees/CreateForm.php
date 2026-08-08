<?php

namespace App\Livewire\Admin\Employees;

use App\Helpers\ActivityLogger;
use App\Models\Employee;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class CreateForm extends Component
{
    public string $name = '';
    public string $nik = '';
    public string $site = '';
    public string $no_telepon = '';
    public string $email = '';
    public string $status = Employee::STATUS_ACTIVE;

    public array $emailSuggestions = [];

    public bool $showCreateUserModal = false;
    public string $newUserName = '';
    public string $newUserEmail = '';
    public string $newUserPassword = 'password';
    public string $newUserRole = 'pengguna';

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
            'email.unique' => 'Email sudah digunakan oleh employee lain.',
            'email.exists' => 'Email harus terdaftar sebagai akun user terlebih dahulu.',
            'status.in' => 'Status harus Active atau Resigned.',
        ];
    }

    public function updatedEmail(string $value): void
    {
        $this->emailSuggestions = [];

        if (trim($value) === '') {
            return;
        }

        $this->emailSuggestions = User::where('email', 'like', '%'.$value.'%')
            ->limit(8)
            ->get(['email', 'name'])
            ->map(fn ($u) => ['email' => $u->email, 'name' => $u->name])
            ->toArray();
    }

    public function getEmailRegisteredProperty(): bool
    {
        return $this->email !== '' && User::where('email', $this->email)->withTrashed()->exists();
    }

    public function getEmailUsedProperty(): bool
    {
        return $this->email !== '' && Employee::where('email', $this->email)->exists();
    }

    public function selectEmail(string $email): void
    {
        $this->email = $email;
        $this->emailSuggestions = [];
        $this->resetValidation('email');
    }

    public function openCreateUserModal(): void
    {
        $this->newUserName = $this->name;
        $this->newUserEmail = $this->email;
        $this->newUserPassword = 'password';
        $this->newUserRole = 'pengguna';
        $this->showCreateUserModal = true;
        $this->dispatch('open-modal', 'create-user');
        $this->resetValidation();
    }

    public function closeCreateUserModal(): void
    {
        $this->showCreateUserModal = false;
        $this->dispatch('close-modal', 'create-user');
        $this->resetValidation();
    }

    public function createUser(): void
    {
        $this->validate([
            'newUserName' => 'required|string|max:255',
            'newUserEmail' => 'required|email|unique:users,email',
            'newUserPassword' => 'required|string|min:6',
            'newUserRole' => 'required|exists:roles,name',
        ], [
            'newUserName.required' => 'Nama wajib diisi.',
            'newUserEmail.required' => 'Email wajib diisi.',
            'newUserEmail.email' => 'Format email tidak valid.',
            'newUserEmail.unique' => 'Email sudah terdaftar.',
            'newUserPassword.required' => 'Password wajib diisi.',
            'newUserPassword.min' => 'Password minimal 6 karakter.',
            'newUserRole.required' => 'Role wajib dipilih.',
            'newUserRole.exists' => 'Role tidak valid.',
        ]);

        $user = User::create([
            'name' => $this->newUserName,
            'email' => $this->newUserEmail,
            'password' => Hash::make($this->newUserPassword),
            'status' => User::STATUS_ACTIVE,
        ]);
        $user->assignRole($this->newUserRole);

        ActivityLogger::log('create', "Menambahkan user baru: {$this->newUserName} ({$this->newUserEmail})", 'App\Models\User', $user->email);

        $this->email = $this->newUserEmail;
        $this->emailSuggestions = [];
        $this->showCreateUserModal = false;
        $this->dispatch('close-modal', 'create-user');
        $this->dispatch('show-toast', message: 'Akun user berhasil dibuat. Email dapat dipakai pada employee ini.', type: 'success');
    }

    public function getRoleList(): array
    {
        return \Spatie\Permission\Models\Role::pluck('name')->toArray();
    }

    public function getRoleLabel(string $role): string
    {
        return match ($role) {
            'admin' => 'Admin',
            'teknisi' => 'Teknisi',
            'supervisor_it' => 'Supervisor IT',
            'manager_it' => 'Manager IT',
            'pengguna' => 'Pengguna',
            default => ucfirst($role),
        };
    }

    public function save(): void
    {
        if ($this->email !== '' && $this->emailUsed) {
            $this->addError('email', 'Email sudah digunakan oleh employee lain.');
            $this->dispatch('show-toast', message: 'Email sudah digunakan oleh employee lain. Data tidak dapat disimpan.', type: 'error');

            return;
        }

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
                $user = User::find($this->email);
                if ($user) {
                    if ($user->status === User::STATUS_RESIGNED) {
                        $employee->update(['akun_login' => 'No Access']);
                    }
                    $user->update(['nik' => $employee->nik]);
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
