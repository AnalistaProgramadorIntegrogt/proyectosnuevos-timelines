<div>
    <div class="space-y-3">
        @forelse($versions as $version)
            @php
                $groupCount = count($version->template_data['groups'] ?? []);
                $taskCount = collect($version->template_data['groups'] ?? [])->sum(fn($g) => count($g['tasks'] ?? []));
            @endphp
            <div class="project-card bg-white dark:bg-surface-dark-card rounded-xl shadow-card border border-gray-100 dark:border-gray-700 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition hover:shadow-elevated">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3">
                        <span class="text-lg font-bold text-gray-800 dark:text-gray-200">v{{ $version->version_number }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $version->status === 'published' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : '' }}
                            {{ $version->status === 'draft' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300' : '' }}
                            {{ $version->status === 'archived' ? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' : '' }}">
                            {{ ucfirst($version->status) }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $groupCount }} grupo(s), {{ $taskCount }} tarea(s)
                    </p>

                    @if($version->notes)
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 italic">
                            "{{ $version->notes }}"
                        </p>
                    @endif

                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-400 dark:text-gray-500">
                        <span>Creado: {{ $version->created_at->format('d/m/Y H:i') }}</span>
                        <span>Actualizado: {{ $version->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    @if($version->status !== 'archived')
                        <a href="{{ route('templates.edit', ['template' => $this->template, 'version' => $version->id]) }}"
                            class="btn-secondary inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition shadow-soft">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar
                        </a>
                        <button wire:click="rollback({{ $version->id }})"
                            wire:confirm="¿Crear una nueva versión basada en esta? Se creará como borrador."
                            class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg border border-amber-200 dark:border-amber-800 text-amber-600 dark:text-amber-400 bg-white dark:bg-gray-700 hover:bg-amber-50 dark:hover:bg-amber-900/30 transition shadow-soft">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Rollback
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-4 text-gray-500 dark:text-gray-400">Esta plantilla no tiene versiones todavía.</p>
            </div>
        @endforelse
    </div>
</div>
