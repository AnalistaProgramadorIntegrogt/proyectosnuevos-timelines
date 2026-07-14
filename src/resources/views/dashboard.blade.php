<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-text-primary">
                    Panel de Control
                </h1>
                <p class="text-sm text-text-muted mt-0.5">Resumen de proyectos y actividad</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- KPI Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                <!-- Total Proyectos -->
                <div class="project-card flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center text-[var(--integro-black)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-text-muted">Total Proyectos</p>
                        <p class="text-3xl font-bold text-text-primary mt-0.5">{{ $totalProyectos }}</p>
                    </div>
                </div>

                <!-- En Evaluación — blue accent -->
                <div class="project-card flex items-start gap-4 border-l-2 border-l-blue-500">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center text-blue-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-text-muted">En Evaluación</p>
                        <p class="text-3xl font-bold text-text-primary mt-0.5">{{ $enEvaluacion }}</p>
                    </div>
                </div>

                <!-- En Proceso (Ejecución) — subtle accent border in brand gray -->
                <div class="project-card flex items-start gap-4 border-l-2 border-l-[var(--integro-gray)]">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center text-[var(--integro-gray)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-text-muted">En Ejecución</p>
                        <p class="text-3xl font-bold text-text-primary mt-0.5">{{ $enProceso }}</p>
                    </div>
                </div>

                <!-- Atrasados — red accent (but semantic: red = needs attention) -->
                <div class="project-card flex items-start gap-4 border-l-2 border-l-[var(--integro-red)]">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center text-[var(--integro-red)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-text-muted">Atrasados</p>
                        <p class="text-3xl font-bold text-text-primary mt-0.5">{{ $atrasados }}</p>
                    </div>
                </div>

                <!-- Entregados / Aprobados — functional green, subordinate -->
                <div class="project-card flex items-start gap-4 border-l-2 border-l-[var(--success)]">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center" style="color: var(--success)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-text-muted">Entregados / Aprobados</p>
                        <p class="text-3xl font-bold text-text-primary mt-0.5">{{ $entregadosAprobados }}</p>
                    </div>
                </div>

                <!-- Rechazados — neutral, use brand gray text -->
                <div class="project-card flex items-start gap-4 border-l-2 border-l-[var(--integro-gray)]">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center text-[var(--integro-gray)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-text-muted">Rechazados</p>
                        <p class="text-3xl font-bold text-text-primary mt-0.5">{{ $rechazados }}</p>
                    </div>
                </div>

                <!-- Culminados — brand black accent -->
                <div class="project-card flex items-start gap-4 border-l-2 border-l-[var(--integro-black)]">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center text-[var(--integro-black)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-text-muted">Culminados</p>
                        <p class="text-3xl font-bold text-text-primary mt-0.5">{{ $culminados }}</p>
                    </div>
                </div>

                <!-- Mis Proyectos — red accent as the most important personal KPI -->
                <div class="project-card flex items-start gap-4 border-l-2 border-l-[var(--integro-red)] sm:col-span-2 lg:col-span-1">
                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center text-[var(--integro-red)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-text-muted">Mis Proyectos</p>
                        <p class="text-3xl font-bold text-text-primary mt-0.5">{{ $misProyectos }}</p>
                    </div>
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="project-card">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-base font-bold text-text-primary">Actividad Reciente</h2>
                    <div class="w-6 h-px bg-[var(--integro-red)]"></div>
                </div>

                @if($recentActivity->count() > 0)
                    <div class="space-y-0">
                        @foreach($recentActivity as $event)
                            <div class="flex items-start gap-3 py-3.5 {{ !$loop->last ? 'border-b border-[var(--border-soft)]' : '' }}">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full border border-[var(--border-soft)] flex items-center justify-center text-[var(--integro-gray-text)]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-semibold text-text-primary">
                                            {{ $event->user?->name ?? 'Sistema' }}
                                        </span>
                                        <span class="text-xs text-text-muted">{{ $event->action }}</span>
                                    </div>
                                    <p class="text-sm text-text-muted mt-0.5">
                                        @if($event->project)
                                            en <a href="{{ route('projects.show', $event->project) }}" class="font-medium text-text-primary underline decoration-[var(--integro-red)] decoration-1 underline-offset-2 hover:decoration-2 transition-all">{{ $event->project->name }}</a>
                                        @endif
                                    </p>
                                    <p class="text-xs text-text-muted mt-1">
                                        {{ $event->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <svg class="w-10 h-10 mx-auto text-[var(--integro-gray-text)]/40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-sm text-text-muted">No hay actividad reciente</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
