<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.admin-layout')] class extends Component {
    public int $id = 0;
}; ?>

<div class="max-w-2xl">
    <livewire:admin.employees.edit-form :id="$id" />
</div>
