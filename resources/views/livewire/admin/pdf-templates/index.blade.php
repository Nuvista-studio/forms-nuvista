<div class="space-y-6"
    x-data x-on:template-updated.window="window.location = '{{ route('admin.pdf-templates.index') }}'">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-primary">PDF Templates</h1>
            <p class="text-sm text-muted mt-1">Kelola template PDF export untuk form Pemeriksaan dan Perawatan</p>
        </div>
    </div>

    {{-- Templates List --}}
    @if($templates->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($templates as $template)
                <div class="glass-card p-5 space-y-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-semibold text-primary">{{ $template->name }}</h3>
                            <p class="text-xs text-muted mt-0.5 font-mono">{{ $template->slug }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $template->is_active ? 'bg-emerald-500/15 text-emerald-400' : 'bg-gray-500/15 text-gray-400' }}">
                            {{ $template->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="text-xs text-muted">
                        <p>Terakhir diupdate: {{ $template->updated_at ? $template->updated_at->format('d M Y H:i') : '-' }}</p>
                        <p>{{ number_format(strlen($template->html_content)) }} karakter</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.pdf-templates.edit', $template->slug) }}" wire:navigate
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-200"
                            style="background: var(--color-primary); color: var(--color-button-text);">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit Template
                        </a>
                        <a href="{{ route('admin.pdf-templates.preview', $template->slug) }}" target="_blank"
                            class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-colors duration-200"
                            style="background: var(--color-glass-bg); border: 1px solid var(--color-border); color: var(--color-text-secondary);">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Preview
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="glass-card p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="mt-3 text-muted">Belum ada template PDF</p>
        </div>
    @endif
</div>
