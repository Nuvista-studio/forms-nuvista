<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.app-layout')] class extends Component
{
    public string $title = 'Form Perawatan';

    public function mount(): void
    {
        if (!auth()->user()->hasAnyRole(['admin', 'teknisi'])) {
            abort(403);
        }
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color: var(--color-text-primary);">
            {{ __('Form Perawatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-sm sm:rounded-lg" style="background: var(--color-bg-card); border: 1px solid var(--color-border);">
                <div class="p-6" style="color: var(--color-text-primary);">
                    {{ __('Halaman pembuatan Form Perawatan perangkat digital.') }}
                </div>
            </div>
        </div>
    </div>
</div>
