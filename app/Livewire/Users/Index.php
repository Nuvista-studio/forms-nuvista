<?php

namespace App\Livewire\Users;

use App\Helpers\ActivityLogger;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $filterName = '';
    public string $filterEmail = '';
    public string $filterNik = '';
    public string $filterDepartment = '';
    public string $filterBusinessUnit = '';
    public string $filterSite = '';
    public string $filterRole = '';
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';
    public bool $showDeleteModal = false;
    public ?int $deleteUserId = null;
    public string $deleteUserName = '';
    public array $selected = [];
    public bool $showBulkDeleteModal = false;
    public bool $showBulkEditModal = false;
    public string $bulkEditField = '';
    public string $bulkEditValue = '';

    protected $queryString = [
        'filterName' => ['except' => ''],
        'filterEmail' => ['except' => ''],
        'filterNik' => ['except' => ''],
        'filterDepartment' => ['except' => ''],
        'filterBusinessUnit' => ['except' => ''],
        'filterSite' => ['except' => ''],
        'filterRole' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updated(string $property): void
    {
        if (str_starts_with($property, 'filter')) {
            $this->resetPage();
        }
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
        $this->selected = array_values(array_diff($this->selected, [$this->deleteUserId]));

        ActivityLogger::log('delete', "Menghapus user: {$this->deleteUserName}", 'App\Models\User', $this->deleteUserId);
        $this->cancelDelete();
        $this->dispatch('user-deleted');
    }

    public function toggleSelectAll(): void
    {
        $pageIds = collect($this->filteredQuery()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12)->items())->pluck('id')->all();

        if (count($pageIds) === count(array_intersect($pageIds, $this->selected))) {
            $this->selected = array_values(array_diff($this->selected, $pageIds));
        } else {
            $this->selected = array_values(array_unique(array_merge($this->selected, $pageIds)));
        }
    }

    public function confirmBulkDelete(): void
    {
        $this->showBulkDeleteModal = true;
    }

    public function cancelBulkDelete(): void
    {
        $this->showBulkDeleteModal = false;
    }

    public function bulkDelete(): void
    {
        $users = User::whereIn('id', $this->selected)->get();
        $deleted = 0;
        foreach ($users as $user) {
            if ($user->id === Auth::id()) continue;
            $user->delete();
            $deleted++;
        }

        ActivityLogger::log('delete', "Menghapus {$deleted} user secara massal");
        $this->selected = [];
        $this->cancelBulkDelete();
        $this->dispatch('show-toast', message: "{$deleted} user berhasil dihapus.", type: 'success');
        $this->dispatch('user-deleted');
    }

    public function openBulkEdit(): void
    {
        $this->bulkEditField = '';
        $this->bulkEditValue = '';
        $this->showBulkEditModal = true;
    }

    public function cancelBulkEdit(): void
    {
        $this->showBulkEditModal = false;
        $this->bulkEditField = '';
        $this->bulkEditValue = '';
    }

    public function bulkEdit(): void
    {
        if (empty($this->selected)) {
            $this->cancelBulkEdit();
            return;
        }

        $allowed = ['role', 'name', 'email', 'nik', 'department', 'business_unit', 'site', 'no_telepon'];
        if (!in_array($this->bulkEditField, $allowed)) {
            $this->addError('bulkEditField', 'Pilih field terlebih dahulu.');
            return;
        }

        if ($this->bulkEditField === 'role') {
            if (!$this->bulkEditValue) {
                $this->addError('bulkEditValue', 'Pilih role terlebih dahulu.');
                return;
            }

            $count = 0;
            foreach (User::whereIn('id', $this->selected)->get() as $user) {
                $user->syncRoles([$this->bulkEditValue]);
                $count++;
            }

            ActivityLogger::log('update', "Mengubah role {$count} user menjadi {$this->bulkEditValue}");
            $this->dispatch('show-toast', message: "Role {$count} user diperbarui menjadi {$this->getRoleLabel($this->bulkEditValue)}.", type: 'success');
        } else {
            $value = trim($this->bulkEditValue);
            $count = User::whereIn('id', $this->selected)->update([$this->bulkEditField => $value ?: null]);

            ActivityLogger::log('update', "Mengubah {$this->bulkEditField} {$count} user menjadi '{$value}'");
            $this->dispatch('show-toast', message: "{$this->getBulkEditFieldLabel($this->bulkEditField)} {$count} user diperbarui.", type: 'success');
        }

        $this->selected = [];
        $this->cancelBulkEdit();
        $this->dispatch('user-updated');
    }

    public function getBulkEditFieldLabel(string $field): string
    {
        return match ($field) {
            'role' => 'Role',
            'name' => 'Nama',
            'email' => 'Email',
            'nik' => 'NIK',
            'department' => 'Department',
            'business_unit' => 'Corp Unit',
            'site' => 'Site',
            'no_telepon' => 'No. Telepon',
            default => ucfirst($field),
        };
    }

    private function filteredQuery()
    {
        return User::with(['roles', 'siteDetail'])
            ->when($this->filterName, fn ($q) => $q->where('name', 'like', "%{$this->filterName}%"))
            ->when($this->filterEmail, fn ($q) => $q->where('email', 'like', "%{$this->filterEmail}%"))
            ->when($this->filterNik, fn ($q) => $q->where('nik', 'like', "%{$this->filterNik}%"))
            ->when($this->filterDepartment, fn ($q) => $q->where('department', 'like', "%{$this->filterDepartment}%"))
            ->when($this->filterBusinessUnit, fn ($q) => $q->where('business_unit', 'like', "%{$this->filterBusinessUnit}%"))
            ->when($this->filterSite, fn ($q) => $q->where(function ($q) {
                $q->where('site', 'like', "%{$this->filterSite}%")
                    ->orWhereHas('siteDetail', fn ($q) => $q->where('site', 'like', "%{$this->filterSite}%"));
            }))
            ->when($this->filterRole, fn ($q) => $q->role($this->filterRole));
    }

    public function render()
    {
        $users = $this->filteredQuery()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);

        $pageIds = collect($users->items())->pluck('id')->all();
        $allSelected = count($pageIds) > 0 && count(array_intersect($this->selected, $pageIds)) === count($pageIds);

        return view('livewire.users.index', [
            'users' => $users,
            'pageIds' => $pageIds,
            'allSelected' => $allSelected,
        ]);
    }
}
