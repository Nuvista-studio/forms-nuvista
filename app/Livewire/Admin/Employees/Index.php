<?php

namespace App\Livewire\Admin\Employees;

use App\Helpers\ActivityLogger;
use App\Models\Employee;
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
    public string $filterStatus = '';
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';
    public bool $showDeleteModal = false;
    public ?int $deleteEmployeeId = null;
    public string $deleteEmployeeName = '';
    public array $selected = [];
    public bool $showBulkDeleteModal = false;

    protected $queryString = [
        'filterName' => ['except' => ''],
        'filterEmail' => ['except' => ''],
        'filterNik' => ['except' => ''],
        'filterDepartment' => ['except' => ''],
        'filterBusinessUnit' => ['except' => ''],
        'filterSite' => ['except' => ''],
        'filterStatus' => ['except' => ''],
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

    public function getStatusBadge(string $status): string
    {
        return $status === Employee::STATUS_RESIGNED
            ? 'bg-gray-500/15 text-gray-400'
            : 'bg-emerald-500/15 text-emerald-400';
    }

    public function getStatusLabel(string $status): string
    {
        return $status === Employee::STATUS_RESIGNED ? 'Resigned' : 'Active';
    }

    public function confirmDelete(int $id, string $name): void
    {
        $this->deleteEmployeeId = $id;
        $this->deleteEmployeeName = $name;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deleteEmployeeId = null;
        $this->deleteEmployeeName = '';
    }

    public function deleteEmployee(): void
    {
        $employee = Employee::findOrFail($this->deleteEmployeeId);

        if ($employee->assignedAssets()->count() > 0) {
            $this->dispatch('delete-error', message: 'Employee masih memiliki asset terpasang. Kembalikan asset terlebih dahulu melalui Form Pengembalian Asset.');
            $this->cancelDelete();
            return;
        }

        $employee->delete();
        $this->selected = array_values(array_diff($this->selected, [$this->deleteEmployeeId]));

        ActivityLogger::log('delete', "Menghapus employee: {$this->deleteEmployeeName}", 'App\Models\Employee', $this->deleteEmployeeId);
        $this->cancelDelete();
        $this->dispatch('employee-deleted');
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
        $employees = Employee::whereIn('id', $this->selected)->get();
        $deleted = 0;
        foreach ($employees as $employee) {
            if ($employee->assignedAssets()->count() > 0) {
                continue;
            }
            $employee->delete();
            $deleted++;
        }

        ActivityLogger::log('delete', "Menghapus {$deleted} employee secara massal");
        $this->selected = [];
        $this->cancelBulkDelete();
        $this->dispatch('show-toast', message: "{$deleted} employee berhasil dihapus.", type: 'success');
        $this->dispatch('employee-deleted');
    }

    private function filteredQuery()
    {
        return Employee::with(['siteDetail', 'user'])
            ->when($this->filterName, fn ($q) => $q->where('name', 'like', "%{$this->filterName}%"))
            ->when($this->filterEmail, fn ($q) => $q->where('email', 'like', "%{$this->filterEmail}%"))
            ->when($this->filterNik, fn ($q) => $q->where('nik', 'like', "%{$this->filterNik}%"))
            ->when($this->filterDepartment, fn ($q) => $q->where('department', 'like', "%{$this->filterDepartment}%"))
            ->when($this->filterBusinessUnit, fn ($q) => $q->where('business_unit', 'like', "%{$this->filterBusinessUnit}%"))
            ->when($this->filterSite, fn ($q) => $q->where(function ($q) {
                $q->where('site', 'like', "%{$this->filterSite}%")
                    ->orWhereHas('siteDetail', fn ($q) => $q->where('site', 'like', "%{$this->filterSite}%"));
            }))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus));
    }

    public function render()
    {
        $employees = $this->filteredQuery()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);

        $pageIds = collect($employees->items())->pluck('id')->all();
        $allSelected = count($pageIds) > 0 && count(array_intersect($this->selected, $pageIds)) === count($pageIds);

        return view('livewire.admin.employees.index', [
            'employees' => $employees,
            'pageIds' => $pageIds,
            'allSelected' => $allSelected,
        ]);
    }
}
