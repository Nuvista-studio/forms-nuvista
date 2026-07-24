<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.app-layout')] class extends Component
{
    public function mount(string $id): void
    {
    }
}; ?>

<div>
    <livewire:assets.detail :id="$id" />
</div>
