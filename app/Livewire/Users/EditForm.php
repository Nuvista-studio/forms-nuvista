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
    public string $department = '';
    public string $business_unit = '';
    public string $site = '';
    public string $no_telepon = '';
    public string $role = '';

    public function mount(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->user = $user;
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->nik = $user->nik ?? '';
        $this->department = $user->department ?? '';
        $this->business_unit = $user->business_unit ?? '';
        $this->site = $user->site ?? '';
        $this->no_telepon = $user->no_telepon ?? '';
        $this->role = $user->getRoleNames()->first() ?? '';
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'nik' => 'nullable|string|max:50|unique:users,nik,' . $this->user->id,
            'department' => 'nullable|string|max:255',
            'business_unit' => 'nullable|string|max:255',
            'site' => 'nullable|string|max:255',
            'no_telepon' => 'nullable|string|max:50',
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
            'role.required' => 'Role wajib dipilih.',
            'role.exists' => 'Role tidak valid.',
        ];
    }

    public function update(): void
    {
        try {
            $this->validate();

            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'nik' => $this->nik ?: null,
                'department' => $this->department ?: null,
                'business_unit' => $this->business_unit ?: null,
                'site' => $this->site ?: null,
                'no_telepon' => $this->no_telepon ?: null,
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            $this->user->update($data);

            $this->user->syncRoles([$this->role]);

            ActivityLogger::log('update', "Mengubah user: {$this->name} ({$this->email})", 'App\Models\User', $this->user->id);
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
