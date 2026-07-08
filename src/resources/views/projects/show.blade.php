<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-text-primary">
                    {{ $project->name }}
                </h1>
                @if($project->code)
                    <p class="text-sm text-text-muted mt-0.5 font-mono">{{ "[$project->code]" }}</p>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Project Info Card -->
            <div class="project-card">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-text-primary">{{ $project->name }}</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                            {{ ucfirst(str_replace('_', ' ', $project->lifecycle_status)) }}
                        </span>
                    </div>
                </div>

                @if($project->description)
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-text-primary mb-2">Descripción</h3>
                        <p class="text-sm text-text-muted max-w-prose">{{ $project->description }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                    <div class="p-3 bg-[var(--surface-muted)]">
                        <span class="text-xs text-text-muted font-medium">Propietario</span>
                        <p class="text-sm font-semibold text-text-primary mt-0.5">{{ $project->owner->name ?? 'N/A' }}</p>
                    </div>
                    <div class="p-3 bg-[var(--surface-muted)]">
                        <span class="text-xs text-text-muted font-medium">Fecha de inicio</span>
                        <p class="text-sm font-semibold text-text-primary mt-0.5">{{ $project->start_date ? $project->start_date->format('d/m/Y') : 'No definida' }}</p>
                    </div>
                    @if($project->outcome)
                        <div class="p-3 bg-[var(--surface-muted)]">
                            <span class="text-xs text-text-muted font-medium">Resultado</span>
                            <p class="text-sm font-semibold text-text-primary mt-0.5">{{ ucfirst($project->outcome) }}</p>
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-[var(--border-soft)]">
                    <a href="{{ route('projects.index') }}" class="btn-secondary text-xs">
                        ← Volver
                    </a>
                    @can('update', $project)
                        <a href="{{ route('projects.edit', $project) }}" class="btn-primary text-xs">
                            Editar Proyecto
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Project Timeline / Groups Section -->
            <div class="project-card">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-base font-bold text-text-primary">Línea de Tiempo</h2>
                    <a href="{{ route('projects.task-list', $project) }}" class="btn-secondary text-xs">
                        Editar tareas
                    </a>
                </div>

                @if($project->groups->isNotEmpty())
                    @foreach($project->groups as $group)
                        <div class="mb-4 p-4 border border-[var(--border-soft)] bg-surface">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-bold text-text-primary">{{ $group->name }}</h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @if($group->status === 'active') bg-amber-50 text-amber-700 border border-amber-200
                                    @elseif($group->status === 'locked') bg-gray-100 text-gray-600 border border-gray-200
                                    @elseif($group->status === 'completed') bg-green-50 text-green-700 border border-green-200
                                    @endif">
                                    {{ $group->status === 'active' ? 'Activo' : ($group->status === 'locked' ? 'Bloqueado' : ($group->status === 'completed' ? 'Completado' : $group->status)) }}
                                </span>
                            </div>

                            @if($group->tasks->isNotEmpty())
                                <div class="space-y-1">
                                    @foreach($group->tasks as $task)
                                        <div class="flex items-center gap-3 py-2 px-2 hover:bg-[var(--surface-muted)] transition-colors">
                                            <span class="w-2 h-2 rounded-full flex-shrink-0
                                                @if($task->status === 'aprobado' || $task->status === 'entregado') bg-[var(--success)]
                                                @elseif($task->status === 'atrasado') bg-[var(--integro-red)]
                                                @elseif($task->status === 'rechazado') bg-[var(--integro-gray)]
                                                @elseif($task->status === 'en_proceso') bg-amber-400
                                                @else bg-[var(--border-soft)]
                                                @endif">
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-text-primary truncate">{{ $task->title }}</p>
                                                @if($task->is_deliverable)
                                                    <span class="text-xs text-[var(--integro-gray)]">Entregable</span>
                                                @endif
                                            </div>
                                            <span class="text-xs px-2 py-0.5 rounded flex-shrink-0
                                                @if($task->status === 'en_proceso') status-en-proceso
                                                @elseif($task->status === 'entregado') status-entregado
                                                @elseif($task->status === 'aprobado') status-aprobado
                                                @elseif($task->status === 'atrasado') status-atrasado
                                                @elseif($task->status === 'rechazado') status-rechazado
                                                @elseif($task->status === 'pending') status-pending
                                                @else bg-gray-100 text-gray-700
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $task->status ?? 'pendiente')) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-text-muted py-2">Sin tareas en este grupo.</p>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-10">
                        <svg class="w-10 h-10 mx-auto text-[var(--integro-gray-text)]/40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-sm text-text-muted">Este proyecto no tiene grupos ni tareas definidas.</p>
                        <a href="{{ route('projects.task-list', $project) }}" class="btn-primary text-xs mt-4 inline-flex">
                            Configurar tareas
                        </a>
                    </div>
                @endif
            </div>

            <!-- Members Section -->
            <div class="project-card">
                @livewire('App\Http\Livewire\Projects\MemberManager', ['project' => $project], key('members-' . $project->id))
            </div>

            <!-- Roles Section -->
            @can('manageRoles', $project)
                <div class="project-card">
                    @livewire('App\Http\Livewire\Projects\RoleManager', ['project' => $project], key('roles-' . $project->id))
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
