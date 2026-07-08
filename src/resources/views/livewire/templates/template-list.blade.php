<div>
    <div class="mb-6">
        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            placeholder="Buscar plantillas..."
            class="w-full sm:w-96 rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 shadow-soft focus:border-primary-400 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
        />
    </div>

    <div class="space-y-4">
        @forelse($templates as $template)
            @php
                $latestVersion = $template->versions->sortByDesc('version_number')->first();
            @endphp
            <div class="project-card bg-white dark:bg-surface-dark-card rounded-xl shadow-card border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition hover:shadow-elevated">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">
                            {{ $template->name }}
                        </h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $template->status === 'published' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : '' }}
                            {{ $template->status === 'draft' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300' : '' }}
                            {{ $template->status === 'archived' ? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' : '' }}">
                            {{ ucfirst($template->status) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">
                        {{ $template->description ?: 'Sin descripción' }}
                    </p>
                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-400 dark:text-gray-500">
                        <span>V{{ $latestVersion?->version_number ?? '—' }}</span>
                        <span>{{ $template->versions->count() }} versión(es)</span>
                        <span>Actualizado: {{ $template->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('templates.edit', $template) }}"
                        class="btn-secondary inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition shadow-soft">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar
                    </a>

                    <a href="{{ route('templates.versions', $template) }}"
                        class="btn-secondary inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition shadow-soft">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Versiones
                    </a>

                    @if($template->status !== 'archived')
                        <button wire:click="deactivate({{ $template->id }})"
                            wire:confirm="¿Desactivar esta plantilla?"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 bg-white dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900/30 transition shadow-soft">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Desactivar
                        </button>
                    @else
                        <button wire:click="activate({{ $template->id }})"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-green-200 dark:border-green-800 text-green-600 dark:text-green-400 bg-white dark:bg-gray-700 hover:bg-green-50 dark:hover:bg-green-900/30 transition shadow-soft">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Reactivar
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7" />
                </svg>
                <p class="mt-4 text-gray-500 dark:text-gray-400">
                    {{ $search ? 'No se encontraron plantillas con ese nombre.' : 'No hay plantillas todavía.' }}
                </p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $templates->links() }}
    </div>
</div>
