@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="bg-surface px-6 py-5 border-b border-[var(--border-soft)]">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-[var(--integro-red)]/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-[var(--integro-red)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-text-primary">{{ $title ?? __('Confirm') }}</h3>
        </div>
    </div>

    <div class="px-6 py-4 text-sm text-text-muted">
        {{ $content }}
    </div>

    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-[var(--border-soft)]">
        {{ $footer }}
    </div>
</x-modal>
