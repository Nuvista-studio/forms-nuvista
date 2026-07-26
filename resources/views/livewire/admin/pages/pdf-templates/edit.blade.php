<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.admin-layout')] class extends Component {
    public string $slug = '';
}; ?>

<div>
    <livewire:admin.pdf-templates.edit-form :$slug />
</div>
