<div class="space-y-3">
    @forelse($projects as $project)
        <a href="{{ route('projects.show', $project) }}" class="flex justify-between items-center p-4 border border-[var(--border-soft)] bg-surface hover:border-[var(--integro-gray)] transition-colors duration-150 group block">
            <div>
                <span class="text-sm font-semibold text-text-primary group-hover:text-[var(--integro-red)] transition-colors">
                    {{ $project->name }}
                </span>
                <div class="flex gap-3 mt-1 text-xs text-text-muted">
                    @if($project->code)
                        <span class="font-mono text-[var(--integro-gray)]">{{ "[$project->code]" }}</span>
                    @endif
                    <span>{{ ucfirst(str_replace('_', ' ', $project->lifecycle_status)) }}</span>
                    @if($project->outcome)
                        <span>{{ ucfirst($project->outcome) }}</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium
                    {{ $project->lifecycle_status === 'finished' ? 'bg-green-50 text-green-700 border border-green-200' :
                       ($project->lifecycle_status === 'ongoing' ? 'bg-amber-50 text-amber-700 border border-amber-200' :
                        'bg-gray-100 text-gray-600 border border-gray-200') }}">
                    {{ $project->lifecycle_status === 'finished' ? 'Finalizado' :
                       ($project->lifecycle_status === 'ongoing' ? 'En curso' : $project->lifecycle_status) }}
                </span>
            </div>
        </a>
    @empty
        <div class="text-center py-12">
            <svg class="w-10 h-10 mx-auto text-[var(--integro-gray-text)]/40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <p class="text-sm text-text-muted">No hay proyectos aún.</p>
            <p class="text-xs text-text-muted mt-1">Crea tu primer proyecto para empezar.</p>
        </div>
    @endforelse
</div>
