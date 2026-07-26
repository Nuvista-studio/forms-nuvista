<?php

namespace App\Livewire\Admin\PdfTemplates;

use App\Models\PdfTemplate;
use Livewire\Component;

class EditForm extends Component
{
    public ?PdfTemplate $templateModel = null;
    public string $slug = '';
    public string $name = '';
    public string $htmlContent = '';
    public bool $isActive = true;

    public function mount(string $slug): void
    {
        $template = PdfTemplate::where('slug', $slug)->firstOrFail();
        $this->templateModel = $template;
        $this->slug = $template->slug;
        $this->name = $template->name;
        $this->htmlContent = $template->html_content;
        $this->isActive = $template->is_active;
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'htmlContent' => 'required|string',
            'isActive' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama template wajib diisi.',
            'htmlContent.required' => 'Konten HTML wajib diisi.',
        ];
    }

    public function save(): void
    {
        try {
            $this->validate();

            $this->templateModel->update([
                'name' => $this->name,
                'html_content' => $this->htmlContent,
                'is_active' => $this->isActive,
            ]);

            $this->templateModel->syncToFile();

            $this->dispatch('template-updated');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('validation-error', errors: $e->errors());
        }
    }

    public function resetToDefault(): void
    {
        $defaultPath = resource_path("views/pdf/{$this->slug}.blade.php");

        if (file_exists($defaultPath)) {
            $this->htmlContent = file_get_contents($defaultPath);
        }
    }

    public function render()
    {
        return view('livewire.admin.pdf-templates.edit-form');
    }
}
