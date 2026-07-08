<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $project->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Project Header Card -->
            <div class="project-card mb-8">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 flex-wrap mb-2">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $project->name }}</h1>
                            @if($project->code)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-muted-100 dark:bg-muted-700 text-muted-600 dark:text-muted-300">
                                    [{{ $project->code }}]
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 flex-wrap text-sm text-muted-500 dark:text-muted-400">
                            <span>{{ $project->owner?->name ?? 'N/A' }}</span>
                            <span class="text-muted-300 dark:text-muted-600">•</span>
                            <span>Inicio: {{ $project->start_date ? $project->start_date->format('d/m/Y') : 'No definida' }}</span>
                            @if($project->outcome)
                                <span class="text-muted-300 dark:text-muted-600">•</span>
                                <span>Resultado: {{ ucfirst($project->outcome) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            @if($project->lifecycle_status === 'en_proceso') status-en-proceso
                            @elseif($project->lifecycle_status === 'entregado') status-entregado
                            @elseif($project->lifecycle_status === 'aprobado') status-aprobado
                            @elseif($project->lifecycle_status === 'rechazado') status-rechazado
                            @elseif($project->lifecycle_status === 'atrasado') status-atrasado
                            @elseif($project->lifecycle_status === 'pending' || $project->lifecycle_status === 'not_started') status-pending
                            @elseif($project->lifecycle_status === 'finished')
                                inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600
                            @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $project->lifecycle_status)) }}
                        </span>
                        @can('update', $project)
                            <a href="{{ route('projects.tasks.edit-list', $project) }}" class="btn-secondary text-xs !px-3 !py-1">
                                Editar lista de tareas
                            </a>
                            <a href="{{ route('projects.edit', $project) }}" class="btn-secondary text-xs !px-3 !py-1">
                                Editar
                            </a>
                        @endcan
                        <a href="{{ route('projects.index') }}" class="btn-secondary text-xs !px-3 !py-1">
                            Volver
                        </a>
                    </div>
                </div>

                @if($project->description)
                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">{{ $project->description }}</p>
                @endif
            </div>

            <!-- Vertical Timeline -->
            @if($timelineData->count() > 0)
                <div class="relative">
                    <!-- Main timeline line -->
                    <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-muted-200 dark:bg-muted-700"></div>

                    <div class="space-y-6">
                        @foreach($timelineData->sortBy('order') as $group)
                            @php
                                $isGate = $group->is_gate;
                                $isLocked = $group->status === 'locked';
                                $totalTasks = $group->tasks->count();
                                $completedTasks = $group->tasks->whereIn('status', ['entregado', 'aprobado', 'rechazado'])->count();
                                $progressPercent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                                $allTasksApproved = $totalTasks > 0 && $group->tasks->every(fn($t) => $t->status === 'aprobado');
                                $hasGateDecision = $group->relationLoaded('gateDecision') && $group->gateDecision;
                            @endphp

                            <!-- Group Section -->
                            <div class="relative pl-16 @if($isLocked) opacity-60 @endif">
                                <!-- Timeline dot -->
                                <div class="absolute left-4 top-6 w-8 h-8 rounded-full border-2 flex items-center justify-center
                                    @if($isLocked)
                                        bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-500
                                    @elseif($isGate)
                                        bg-amber-100 dark:bg-amber-900/30 border-amber-400 dark:border-amber-500 text-amber-600 dark:text-amber-400
                                    @else
                                        bg-white dark:bg-surface-dark-card border-primary-400 dark:border-primary-500 text-primary-600 dark:text-primary-400
                                    @endif">
                                    @if($isLocked)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    @elseif($isGate)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    @endif
                                </div>

                                <!-- Group Card -->
                                <div class="project-card @if($isGate && !$isLocked) border-amber-200 dark:border-amber-700 @endif">
                                    <!-- Group Header -->
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-semibold
                                                @if($isLocked) text-muted-400 dark:text-muted-500
                                                @else text-gray-900 dark:text-white @endif">
                                                @if($isLocked)
                                                    🔒
                                                @endif
                                                {{ $group->name }}
                                            </h3>
                                            @if($isGate && !$isLocked)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-700">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                    Dependencia
                                                </span>
                                            @endif
                                            @if($group->status)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                    @if($group->status === 'en_proceso') status-en-proceso
                                                    @elseif($group->status === 'entregado') status-entregado
                                                    @elseif($group->status === 'aprobado') status-aprobado
                                                    @elseif($group->status === 'rechazado') status-rechazado
                                                    @elseif($group->status === 'atrasado') status-atrasado
                                                    @elseif($group->status === 'pending') status-pending
                                                    @elseif($group->status === 'locked')
                                                        bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600
                                                    @elseif($group->status === 'gate_rejected')
                                                        bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-700
                                                    @elseif($group->status === 'completed_viable')
                                                        bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-700
                                                    @elseif($group->status === 'completed_nonviable')
                                                        bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-700
                                                    @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                                    @endif">
                                                    @if($group->status === 'locked') 🔒 @endif
                                                    {{ ucfirst(str_replace('_', ' ', $group->status)) }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($totalTasks > 0 && !$isLocked)
                                            <span class="text-xs text-muted-500 dark:text-muted-400">
                                                {{ $completedTasks }}/{{ $totalTasks }} tareas
                                            </span>
                                        @endif
                                    </div>

                                    @if(!$isLocked)
                                        <!-- Progress Bar -->
                                        @if($totalTasks > 0)
                                            <div class="w-full h-1.5 bg-muted-100 dark:bg-muted-700 rounded-full mb-4">
                                                <div class="h-1.5 rounded-full transition-all duration-500
                                                    @if($progressPercent === 100) bg-emerald-500
                                                    @elseif($progressPercent > 50) bg-primary-500
                                                    @elseif($progressPercent > 0) bg-amber-500
                                                    @else bg-muted-300 dark:bg-muted-600
                                                    @endif"
                                                    style="width: {{ $progressPercent }}%">
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Dependency Arrow -->
                                        @if($group->unlocks_group_id)
                                            @php
                                                $unlockedGroup = $timelineData->firstWhere('id', $group->unlocks_group_id);
                                            @endphp
                                            @if($unlockedGroup)
                                                <div class="flex items-center gap-2 mb-4 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-xs text-blue-700 dark:text-blue-300">
                                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                    </svg>
                                                    <span>Se activa después de: <strong>{{ $unlockedGroup->name }}</strong></span>
                                                </div>
                                            @endif
                                        @endif

                                        <!-- Gate Decision Panel (handles showing existing decision OR decision form OR pending message) -->
                                        @if($isGate)
                                            <div class="mb-4">
                                                @livewire('App\Http\Livewire\Projects\GateDecisionPanel', ['group' => $group], key('gate-' . $group->id))
                                            </div>
                                        @elseif($hasGateDecision)
                                            <!-- Legacy: non-gate groups with gate decisions (edge case) -->
                                            <div class="mb-4 px-4 py-3 rounded-lg
                                                @if($group->gateDecision->outcome === 'viable' || $group->gateDecision->outcome === 'aprobado') bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300
                                                @else bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300
                                                @endif">
                                                <div class="flex items-center gap-2 text-sm">
                                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                    </svg>
                                                    <span class="font-medium">Decisión de Gate:</span>
                                                    <span class="capitalize">{{ $group->gateDecision->outcome }}</span>
                                                    @if($group->gateDecision->decisionMaker)
                                                        <span class="text-muted-500">por {{ $group->gateDecision->decisionMaker->name }}</span>
                                                    @endif
                                                </div>
                                                @if($group->gateDecision->notes)
                                                    <p class="mt-1 text-xs opacity-80">{{ $group->gateDecision->notes }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <!-- Locked group message -->
                                        <div class="flex items-center gap-3 py-4 px-3">
                                            <svg class="w-5 h-5 text-muted-400 dark:text-muted-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                            </svg>
                                            <p class="text-sm text-muted-500 dark:text-muted-400 italic">
                                                Este grupo se desbloqueará cuando el grupo anterior complete su gate
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Tasks within Group (only show if not locked) -->
                                    @if(!$isLocked && $group->tasks->count() > 0)
                                        <div class="space-y-3">
                                            @foreach($group->tasks->sortBy('order') as $task)
                                                @php
                                                    $completedSubtasks = $task->subtasks->where('status', 'entregado')->count();
                                                    $totalSubtasks = $task->subtasks->count();
                                                    $hasDeliverable = $task->is_deliverable && $task->deliverableVersions->count() > 0;
                                                    $taskVisibilityKey = 'task_' . $task->id;
                                                    $groupVisibilityKey = 'group_' . $group->id;
                                                    $taskRule = $visibilityRules[$taskVisibilityKey] ?? $visibilityRules[$groupVisibilityKey] ?? null;
                                                    $canViewTask = $taskRule ? $taskRule['can_view'] : true;
                                                    $canEditTask = $taskRule ? $taskRule['can_edit'] : $canEditTasks;
                                                @endphp

                                                <div class="group relative flex items-start gap-3 p-4 rounded-xl border border-muted-100 dark:border-muted-700 @if($canViewTask) bg-surface-elevated dark:bg-surface-dark-elevated hover:shadow-soft @else bg-muted dark:bg-muted/20 opacity-90 @endif transition-all duration-150">
                                                    <!-- Task status dot -->
                                                    <div class="flex-shrink-0 mt-0.5">
                                                        <div class="w-3 h-3 rounded-full border-2
                                                            @if($task->status === 'entregado' || $task->status === 'aprobado') bg-emerald-500 border-emerald-500
                                                            @elseif($task->status === 'atrasado') bg-red-400 border-red-400
                                                            @elseif($task->status === 'rechazado') bg-gray-400 border-gray-400
                                                            @elseif($task->status === 'en_proceso') bg-amber-400 border-amber-400
                                                            @else bg-muted-200 dark:bg-muted-600 border-muted-300 dark:border-muted-500
                                                            @endif">
                                                        </div>
                                                    </div>

                                                    <!-- Task content -->
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-start justify-between gap-2">
                                                            <div class="min-w-0">
                                                                <div class="flex items-center gap-2 flex-wrap">
                                                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $task->title }}</h4>
                                                                    @if(!$canViewTask)
                                                                    @endif
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                                        @if($task->status === 'en_proceso') status-en-proceso
                                                                        @elseif($task->status === 'entregado') status-entregado
                                                                        @elseif($task->status === 'aprobado') status-aprobado
                                                                        @elseif($task->status === 'atrasado') status-atrasado
                                                                        @elseif($task->status === 'rechazado') status-rechazado
                                                                        @elseif($task->status === 'pending') status-pending
                                                                        @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                                                        @endif">
                                                                        {{ ucfirst(str_replace('_', ' ', $task->status ?? 'pendiente')) }}
                                                                    </span>
                                                                    @if($task->is_deliverable)
                                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-700">
                                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                            </svg>
                                                                            Entregable
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <!-- Actions -->
                                                            @if($canViewTask)
                                                            <div class="flex items-center gap-1 flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                                                <a href="{{ route('tasks.show', $task) }}" class="p-1.5 rounded-lg text-muted-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors" title="Ver tarea">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                    </svg>
                                                                </a>
                                                                @if($canEditTask)
                                                                    <a href="{{ route('tasks.edit', $task) }}" class="p-1.5 rounded-lg text-muted-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors" title="Editar tarea">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                                        </svg>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                            @endif
                                                        </div>

                                                        @if($task->description)
                                                            <p class="mt-1 text-xs text-muted-500 dark:text-muted-400 line-clamp-2">{{ Str::limit($task->description, 120) }}</p>
                                                        @endif

                                                        <!-- Task metadata -->
                                                        <div class="mt-2 flex items-center gap-4 flex-wrap text-xs text-muted-500 dark:text-muted-400">
                                                            @if($task->responsible)
                                                                <span class="inline-flex items-center gap-1">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                    </svg>
                                                                    {{ $task->responsible->name }}
                                                                </span>
                                                            @endif
                                                            @if($task->calculated_end_date)
                                                                <span class="inline-flex items-center gap-1">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                    </svg>
                                                                    {{ $task->calculated_end_date->format('d/m/Y') }}
                                                                </span>
                                                            @endif
                                                            @if($task->duration_days)
                                                                <span class="inline-flex items-center gap-1">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    {{ $task->duration_days }} días
                                                                </span>
                                                            @endif
                                                            @if($totalSubtasks > 0)
                                                                <span class="inline-flex items-center gap-1">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                                    </svg>
                                                                    Subtareas: {{ $completedSubtasks }}/{{ $totalSubtasks }}
                                                                </span>
                                                            @endif
                                                            @if($hasDeliverable && $canViewTask)
                                                                <span class="inline-flex items-center gap-1 text-purple-600 dark:text-purple-400">
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                                    </svg>
                                                                    {{ $task->deliverableVersions->count() }} versión(es)
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif(!$isLocked)
                                        <p class="text-sm text-muted-400 dark:text-muted-500 italic py-3 text-center">
                                            No hay tareas en este grupo
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Empty state -->
                <div class="project-card text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-muted-300 dark:text-muted-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Sin grupos de trabajo</h3>
                    <p class="text-sm text-muted-500 dark:text-muted-400 mb-4">
                        Este proyecto aún no tiene grupos ni tareas definidas.
                    </p>
                    @can('update', $project)
                        <a href="{{ route('projects.edit', $project) }}" class="btn-primary">
                            Configurar proyecto
                        </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>

    <!-- Members Section -->
    <div class="mt-6">
        @livewire('App\Http\Livewire\Projects\MemberManager', ['project' => $project], key('members-' . $project->id))
    </div>

    <!-- Roles Section -->
    @can('manageRoles', $project)
        <div class="mt-6">
            @livewire('App\Http\Livewire\Projects\RoleManager', ['project' => $project], key('roles-' . $project->id))
        </div>
    @endcan
</x-app-layout>
