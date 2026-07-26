<?php

namespace App\Livewire\Admin\PdfTemplates;

use App\Models\PdfTemplate;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $templates = PdfTemplate::orderBy('name')->get();

        return view('livewire.admin.pdf-templates.index', [
            'templates' => $templates,
        ]);
    }
}
