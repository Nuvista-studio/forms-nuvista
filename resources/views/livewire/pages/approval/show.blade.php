<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.app-layout')] class extends Component
{
    public function mount(string $type, string $id): void
    {
        $allowed = ['pemeriksaan', 'perawatan'];
        if (!in_array($type, $allowed)) {
            abort(404);
        }
    }
}; ?>

<div>
    <livewire:approval.review-form :type="$type" :id="$id" />
</div>
