<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = '';
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';
    public bool $showDeleteModal = false;
    public ?int $deleteUserId = null;
    public string $deleteUserName = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterRole' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterRole(): void
    {
        $this->resetPage();
    }

    public function toggleSort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function getRoleList(): array
    {
        return \Spatie\Permission\Models\Role::pluck('name')->toArray();
    }

    public function getRoleBadge(string $role): string
    {
        return match ($role) {
            'admin' => 'bg-red-500/15 text-red-400',
            'teknisi' => 'bg-blue-500/15 text-blue-400',
            'supervisor_it' => 'bg-yellow-500/15 text-yellow-400',
            'manager_it' => 'bg-purple-500/15 text-purple-400',
            'pengguna' => 'bg-emerald-500/15 text-emerald-400',
            default => 'bg-gray-500/15 text-gray-400',
        };
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

    public function confirmDelete(int $id, string $name): void
    {
        $this->deleteUserId = $id;
        $this->deleteUserName = $name;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteUserId = null;
        $this->deleteUserName = '';
    }

    public function deleteUser(): void
    {
        if ($this->deleteUserId === Auth::id()) {
            $this->dispatch('delete-error', message: 'Tidak bisa menghapus akun sendiri.');
            $this->cancelDelete();
            return;
        }

        User::findOrFail($this->deleteUserId)->delete();

        $this->cancelDelete();
        $this->dispatch('user-deleted');
    }

    public function render()
    {
        $query = User::with('roles')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('nik', 'like', "%{$this->search}%")
                    ->orWhere('department', 'like', "%{$this->search}%");
            }))
            ->when($this->filterRole, fn ($q) => $q->role($this->filterRole))
            ->orderBy($this->sortBy, $this->sortDirection);

        $users = $query->paginate(12);

        return view('livewire.users.index', [
            'users' => $users,
        ]);
    }
}
