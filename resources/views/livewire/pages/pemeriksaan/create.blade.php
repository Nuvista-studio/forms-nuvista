<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.app-layout')] class extends Component
{
    public function mount(): void
    {
        if (!auth()->user()->hasAnyRole(['admin', 'teknisi'])) {
            abort(403);
        }
    }
}; ?>

<div>
    <livewire:pemeriksaan.create-form />
</div>
