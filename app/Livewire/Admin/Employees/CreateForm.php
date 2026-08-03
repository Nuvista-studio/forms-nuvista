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
    public string $department = '';
    public string $business_unit = '';
    public string $site = '';
    public string $no_telepon = '';
    public string $email = '';
    public string $status = Employee::STATUS_ACTIVE;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:255',
            'business_unit' => 'nullable|string|max:50|exists:sites,id_corp',
            'site' => 'nullable|string|max:50|exists:sites,id_site',
            'no_telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'status' => 'nullable|in:active,resigned',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.email' => 'Format email tidak valid.',
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
                'department' => $this->department ?: null,
                'business_unit' => $this->business_unit ?: null,
                'site' => $this->site ?: null,
                'no_telepon' => $this->no_telepon ?: null,
                'email' => $this->email ?: null,
                'status' => $this->status,
            ]);

            ActivityLogger::log('create', "Menambahkan employee baru: {$this->name}", 'App\Models\Employee', $employee->id);
            $this->dispatch('employee-created');
            $this->redirect(route('admin.employees.edit', $employee->id));
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('validation-error', errors: $e->errors());
        }
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

    public function render()
    {
        return view('livewire.admin.employees.create-form');
    }
}
