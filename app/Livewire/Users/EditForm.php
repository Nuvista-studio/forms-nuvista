<?php

namespace App\Livewire\Users;

use App\Helpers\ActivityLogger;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class EditForm extends Component
{
    public ?User $user = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $nik = '';
    public string $status = User::STATUS_ACTIVE;
    public string $role = '';

    public array $assignedAssets = [];

    public function mount(string $email): void
    {
        $user = User::findOrFail($email);
        $this->user = $user;
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->nik = $user->nik ?? '';
        $this->status = $user->status ?? User::STATUS_ACTIVE;
        $this->role = $user->getRoleNames()->first() ?? '';
        $this->assignedAssets = $user->assignedAssets()
            ->get(['id', 'no_asset', 'nama_perangkat', 'brand', 'tipe', 'no_serial'])
            ->toArray();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->email,
            'password' => 'nullable|string|min:6|confirmed',
            'nik' => 'nullable|string|max:50|unique:users,nik,' . $this->user->email,
            'status' => 'nullable|in:Enable,Disable',
            'role' => 'required|exists:roles,name',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'status.in' => 'Status harus Active atau Resigned.',
            'role.required' => 'Role wajib dipilih.',
            'role.exists' => 'Role tidak valid.',
        ];
    }

    public function update(): void
    {
        try {
            $this->validate();

            if ($this->status === User::STATUS_RESIGNED && ! empty($this->assignedAssets)) {
                $this->addError(
                    'status',
                    'User masih memiliki ' . count($this->assignedAssets) . ' asset terpasang. Kembalikan asset terlebih dahulu melalui Form Pengembalian Asset.'
                );

                return;
            }

            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'nik' => $this->nik ?: null,
                'status' => $this->status,
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            $this->user->update($data);

            $this->user->syncEmployeeLink();
            $this->user->syncRoles([$this->role]);

            ActivityLogger::log('update', "Mengubah user: {$this->name} ({$this->email})", 'App\Models\User', $this->user->email);
            $this->dispatch('user-updated');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('validation-error', errors: $e->errors());
        }
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

    public function render()
    {
        return view('livewire.users.edit-form');
    }
}
