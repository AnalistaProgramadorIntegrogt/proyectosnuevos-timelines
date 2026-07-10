<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $task->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Task Header Card -->
            <div class="project-card">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 flex-wrap mb-2">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $task->title }}</h1>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if($task->status === 'en_proceso') status-en-proceso
                                @elseif($task->status === 'entregado') status-entregado
                                @elseif($task->status === 'aprobado') status-aprobado
                                @elseif($task->status === 'atrasado') status-atrasado
                                @elseif($task->status === 'rechazado') status-rechazado
                                @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $task->status ?? 'pendiente')) }}
                            </span>
                            @if($task->is_deliverable)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-700">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Entregable
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-700">
                                    No entregable
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 flex-wrap text-sm text-muted-500 dark:text-muted-400">
                            @if($task->projectGroup)
                                <a href="{{ route('projects.show', $project) }}#group-{{ $task->projectGroup->id }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                    {{ $project->name }} / {{ $task->projectGroup->name }}
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if($canEdit)
                            <a href="{{ route('tasks.edit', $task) }}" class="btn-secondary text-xs !px-3 !py-1">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </a>
                        @endif
                        <a href="{{ route('projects.show', $project) }}" class="btn-secondary text-xs !px-3 !py-1">
                            Volver al proyecto
                        </a>
                    </div>
                </div>
            </div>

            <!-- Task Details Card -->
            <div class="project-card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detalles de la Tarea</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-surface-elevated dark:bg-surface-dark-elevated rounded-lg p-4">
                        <span class="text-xs text-muted-500 dark:text-muted-400 uppercase tracking-wider font-medium">Grupo</span>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $task->projectGroup?->name ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-surface-elevated dark:bg-surface-dark-elevated rounded-lg p-4">
                        <span class="text-xs text-muted-500 dark:text-muted-400 uppercase tracking-wider font-medium">Orden</span>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">#{{ $task->order }}</p>
                    </div>
                    <div class="bg-surface-elevated dark:bg-surface-dark-elevated rounded-lg p-4">
                        <span class="text-xs text-muted-500 dark:text-muted-400 uppercase tracking-wider font-medium">Duración</span>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $task->duration_days }} día(s)</p>
                    </div>
                    <div class="bg-surface-elevated dark:bg-surface-dark-elevated rounded-lg p-4">
                        <span class="text-xs text-muted-500 dark:text-muted-400 uppercase tracking-wider font-medium">Fecha de inicio calculada</span>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                            {{ $task->calculated_start_date ? $task->calculated_start_date->format('d/m/Y') : '—' }}
                        </p>
                    </div>
                    <div class="bg-surface-elevated dark:bg-surface-dark-elevated rounded-lg p-4">
                        <span class="text-xs text-muted-500 dark:text-muted-400 uppercase tracking-wider font-medium">Fecha de fin calculada</span>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                            {{ $task->calculated_end_date ? $task->calculated_end_date->format('d/m/Y') : '—' }}
                        </p>
                    </div>
                    <div class="bg-surface-elevated dark:bg-surface-dark-elevated rounded-lg p-4">
                        <span class="text-xs text-muted-500 dark:text-muted-400 uppercase tracking-wider font-medium">Tipo</span>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                            {{ $task->is_deliverable ? 'Entregable' : 'No entregable' }}
                        </p>
                    </div>
                    <div class="bg-surface-elevated dark:bg-surface-dark-elevated rounded-lg p-4">
                        <span class="text-xs text-muted-500 dark:text-muted-400 uppercase tracking-wider font-medium">Responsable</span>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                            {{ $task->responsible?->name ?? '—' }}
                        </p>
                    </div>
                    <div class="bg-surface-elevated dark:bg-surface-dark-elevated rounded-lg p-4">
                        <span class="text-xs text-muted-500 dark:text-muted-400 uppercase tracking-wider font-medium">Aprobador efectivo</span>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                            {{ $effectiveApprover?->name ?? '—' }}
                            @if($task->explicit_approver_id)
                                <span class="ml-1 text-xs text-primary-500">(asignado)</span>
                            @elseif($project->default_approver_id)
                                <span class="ml-1 text-xs text-muted-400">(por defecto del proyecto)</span>
                            @endif
                        </p>
                    </div>
                    <div class="bg-surface-elevated dark:bg-surface-dark-elevated rounded-lg p-4">
                        <span class="text-xs text-muted-500 dark:text-muted-400 uppercase tracking-wider font-medium">Obligatoria</span>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                            {{ $task->is_required ? 'Sí' : 'No' }}
                        </p>
                    </div>
                </div>
                @if($task->description)
                    <div class="mt-4 pt-4 border-t border-muted-100 dark:border-muted-700">
                        <span class="text-xs text-muted-500 dark:text-muted-400 uppercase tracking-wider font-medium">Descripción</span>
                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $task->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Subtasks Section -->
            <div class="project-card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Subtareas
                    @if($task->subtasks->count() > 0)
                        <span class="ml-2 text-sm font-normal text-muted-500 dark:text-muted-400">({{ $task->subtasks->count() }})</span>
                    @endif
                </h3>
                @if($task->subtasks->count() > 0)
                    <div class="space-y-3">
                        @foreach($task->subtasks as $subtask)
                            <div class="flex items-start gap-3 p-4 rounded-xl border border-muted-100 dark:border-muted-700 bg-surface-elevated dark:bg-surface-dark-elevated">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div class="w-3 h-3 rounded-full border-2
                                        @if($subtask->status === 'entregado' || $subtask->status === 'aprobado') bg-emerald-500 border-emerald-500
                                        @elseif($subtask->status === 'atrasado') bg-red-400 border-red-400
                                        @elseif($subtask->status === 'rechazado') bg-gray-400 border-gray-400
                                        @elseif($subtask->status === 'en_proceso') bg-amber-400 border-amber-400
                                        @else bg-muted-200 dark:bg-muted-600 border-muted-300 dark:border-muted-500
                                        @endif">
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $subtask->title }}</h4>
                                            @if($subtask->status)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mt-1
                                                    @if($subtask->status === 'en_proceso') status-en-proceso
                                                    @elseif($subtask->status === 'entregado') status-entregado
                                                    @elseif($subtask->status === 'aprobado') status-aprobado
                                                    @elseif($subtask->status === 'atrasado') status-atrasado
                                                    @elseif($subtask->status === 'rechazado') status-rechazado
                                                    @elseif($subtask->status === 'pending') status-pending
                                                    @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                                    @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $subtask->status)) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($subtask->description)
                                        <p class="mt-1 text-xs text-muted-500 dark:text-muted-400">{{ $subtask->description }}</p>
                                    @endif
                                    <div class="mt-2 flex items-center gap-4 flex-wrap text-xs text-muted-500 dark:text-muted-400">
                                        @if($subtask->responsible)
                                            <span>{{ $subtask->responsible->name }}</span>
                                        @endif
                                        @if($subtask->duration_days)
                                            <span>{{ $subtask->duration_days }} día(s)</span>
                                        @endif
                                        @if($subtask->start_date)
                                            <span>Inicio: {{ $subtask->start_date->format('d/m/Y') }}</span>
                                        @endif
                                        @if($subtask->end_date)
                                            <span>Fin: {{ $subtask->end_date->format('d/m/Y') }}</span>
                                        @endif
                                    </div>
                                    
                                    @if($subtask->is_deliverable)
                                        <div class="mt-4 pt-4 border-t border-muted-100 dark:border-muted-700">
                                            @if($subtask->status === 'entregado')
                                                @livewire('tasks.approval-panel', ['task' => $task, 'subtask' => $subtask], key('approval-subtask-' . $subtask->id))
                                            @elseif($subtask->status === 'en_proceso' || $subtask->status === 'rechazado' || $subtask->status === 'atrasado')
                                                @if($subtask->status === 'rechazado')
                                                    <div class="mb-3 bg-red-50 dark:bg-red-900/20 rounded-xl p-3 border border-red-200 dark:border-red-800">
                                                        <p class="text-xs font-medium text-red-700 dark:text-red-300">❌ Subtarea rechazada</p>
                                                    </div>
                                                @endif
                                                @livewire('tasks.deliverable-upload', ['task' => $task, 'subtask' => $subtask], key('upload-subtask-' . $subtask->id))
                                            @elseif($subtask->status === 'aprobado')
                                                <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-3 border border-emerald-200 dark:border-emerald-800">
                                                    <p class="text-xs font-medium text-emerald-700 dark:text-emerald-300">✅ Subtarea aprobada</p>
                                                    @livewire('tasks.deliverable-upload', ['task' => $task, 'subtask' => $subtask], key('upload-subtask-' . $subtask->id))
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-muted-400 dark:text-muted-500 italic py-3 text-center">No hay subtareas definidas.</p>
                @endif
            </div>

            <!-- Deliverable Versions Section -->
            @if($task->is_deliverable)
                <div class="project-card">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Versiones del Entregable
                        @if($task->deliverableVersions->count() > 0)
                            <span class="ml-2 text-sm font-normal text-muted-500 dark:text-muted-400">({{ $task->deliverableVersions->count() }})</span>
                        @endif
                    </h3>
                    @if($task->deliverableVersions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-muted-100 dark:border-muted-700">
                                        <th class="text-left py-3 px-4 font-medium text-muted-500 dark:text-muted-400">Versión</th>
                                        <th class="text-left py-3 px-4 font-medium text-muted-500 dark:text-muted-400">Archivo</th>
                                        <th class="text-left py-3 px-4 font-medium text-muted-500 dark:text-muted-400">Subido por</th>
                                        <th class="text-left py-3 px-4 font-medium text-muted-500 dark:text-muted-400">Fecha</th>
                                        <th class="text-left py-3 px-4 font-medium text-muted-500 dark:text-muted-400">Nota</th>
                                        <th class="text-right py-3 px-4 font-medium text-muted-500 dark:text-muted-400">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($task->deliverableVersions as $version)
                                        <tr class="border-b border-muted-50 dark:border-muted-800 hover:bg-surface-elevated dark:hover:bg-surface-dark-elevated transition-colors">
                                            <td class="py-3 px-4 font-semibold text-gray-900 dark:text-white">v{{ $version->version_number }}</td>
                                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300 max-w-[200px] truncate">{{ $version->original_filename }}</td>
                                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300">{{ $version->uploader?->name ?? '—' }}</td>
                                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300">{{ $version->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                            <td class="py-3 px-4 text-muted-500 dark:text-muted-400 max-w-[150px] truncate">{{ $version->upload_note ?? '—' }}</td>
                                            <td class="py-3 px-4 text-right">
                                                <a href="{{ route('deliverables.download', $version) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-900/30 transition-colors">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                    Descargar
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-muted-400 dark:text-muted-500 italic py-3 text-center">
                            No se han subido versiones de este entregable.
                        </p>
                    @endif
                </div>

                <div class="mt-6">
                    @livewire('tasks.deliverable-upload', ['task' => $task], key('upload-' . $task->id))
                </div>
            @endif

            <!-- Submissions Section -->
            <div class="project-card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Historial de Entregas
                    @if($task->submissions->count() > 0)
                        <span class="ml-2 text-sm font-normal text-muted-500 dark:text-muted-400">({{ $task->submissions->count() }})</span>
                    @endif
                </h3>
                @if($task->submissions->count() > 0)
                    <div class="space-y-3">
                        @foreach($task->submissions as $submission)
                            <div class="p-4 rounded-xl border border-muted-100 dark:border-muted-700 bg-surface-elevated dark:bg-surface-dark-elevated">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white">Entrega #{{ $loop->iteration }}</span>
                                            <span class="text-xs text-muted-500 dark:text-muted-400">{{ $submission->created_at?->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <div class="mt-1 flex items-center gap-4 flex-wrap text-xs text-muted-500 dark:text-muted-400">
                                            @if($submission->submitter)
                                                <span>Por: {{ $submission->submitter->name }}</span>
                                            @endif
                                            @if($submission->deliverableVersion)
                                                <span>Versión: v{{ $submission->deliverableVersion->version_number }}</span>
                                            @endif
                                        </div>
                                        @if($submission->notes)
                                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">{{ $submission->notes }}</p>
                                        @endif

                                        <!-- Approval Decision -->
                                        @if($submission->relationLoaded('approvalDecision') && $submission->approvalDecision)
                                            <div class="mt-3 px-3 py-2 rounded-lg text-xs
                                                @if($submission->approvalDecision->decision === 'aprobado') bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300
                                                @elseif($submission->approvalDecision->decision === 'rechazado') bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300
                                                @else bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300
                                                @endif">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                    </svg>
                                                    <span class="font-medium capitalize">{{ $submission->approvalDecision->decision }}</span>
                                                    @if($submission->approvalDecision->approver)
                                                        <span>por {{ $submission->approvalDecision->approver->name }}</span>
                                                    @endif
                                                </div>
                                                @if($submission->approvalDecision->note)
                                                    <p class="mt-1 opacity-80">{{ $submission->approvalDecision->note }}</p>
                                                @endif
                                            </div>
                                        @else
                                            <div class="mt-3 px-3 py-2 rounded-lg text-xs bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300">
                                                Pendiente de aprobación
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-muted-400 dark:text-muted-500 italic py-3 text-center">
                        No hay entregas registradas para esta tarea.
                    </p>
                @endif
            </div>

            <!-- Approval History / Audit Events -->
            @if($task->relationLoaded('auditEvents') && $task->auditEvents->count() > 0)
                <div class="project-card">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Historial de Auditoría
                        <span class="ml-2 text-sm font-normal text-muted-500 dark:text-muted-400">({{ $task->auditEvents->count() }})</span>
                    </h3>
                    <div class="space-y-2">
                        @foreach($task->auditEvents->sortByDesc('created_at') as $event)
                            <div class="flex items-start gap-3 p-3 rounded-lg bg-surface-elevated dark:bg-surface-dark-elevated">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div class="w-2 h-2 rounded-full
                                        @if(str_contains($event->action, 'approv') || str_contains($event->action, 'aprob')) bg-emerald-500
                                        @elseif(str_contains($event->action, 'reject') || str_contains($event->action, 'rech')) bg-red-500
                                        @elseif(str_contains($event->action, 'submit')) bg-blue-500
                                        @elseif(str_contains($event->action, 'reopen')) bg-amber-500
                                        @else bg-gray-400
                                        @endif">
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="font-medium text-gray-900 dark:text-white">{{ str_replace('_', ' ', ucfirst($event->action)) }}</span>
                                        @if($event->user)
                                            <span class="text-muted-500 dark:text-muted-400">por {{ $event->user->name }}</span>
                                        @endif
                                        <span class="text-xs text-muted-400">{{ $event->created_at?->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if($event->reason)
                                        <p class="mt-1 text-xs text-muted-500 dark:text-muted-400">{{ $event->reason }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($task->is_deliverable && $task->status === 'entregado')
                <div class="project-card">
                    @livewire('tasks.approval-panel', ['task' => $task], key('approval-' . $task->id))
                </div>
            @elseif($task->is_deliverable && $task->status === 'en_proceso')
                <div class="bg-surface-elevated dark:bg-surface-dark-elevated rounded-xl p-4 border border-muted-100 dark:border-muted-700">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-primary-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Esperando entrega</p>
                            <p class="text-xs text-muted-500 dark:text-muted-400 mt-1">
                                Esta tarea requiere que subas un archivo entregable.
                                Una vez lo subas, el estado cambiará automáticamente a <strong>entregado</strong>
                                y <strong>{{ $effectiveApprover?->name ?? 'el aprobador' }}</strong> podrá aprobarla o rechazarla.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($task->status === 'aprobado')
                <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4 border border-emerald-200 dark:border-emerald-800">
                    <p class="text-sm font-medium text-emerald-700 dark:text-emerald-300">✅ Tarea aprobada</p>
                </div>
            @elseif($task->status === 'rechazado')
                <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 border border-red-200 dark:border-red-800">
                    <p class="text-sm font-medium text-red-700 dark:text-red-300">❌ Tarea rechazada</p>
                </div>
            @endif

            <!-- Actions Section -->
            <div class="project-card">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Acciones</h3>
                <div class="flex items-center gap-3 flex-wrap">
                    @if($canSubmit)
                        <form method="POST" action="{{ route('tasks.submit', $task) }}" class="inline">
                            @csrf
                            <button type="submit" class="btn-primary text-sm"
                                onclick="return confirm('¿Estás seguro de entregar esta tarea para aprobación?')">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Entregar para Aprobación
                            </button>
                        </form>
                    @endif

                    @if($canReopen)
                        <div x-data="{ showReopenForm: false }">
                            <button @click="showReopenForm = true" class="btn-secondary text-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reabrir Tarea
                            </button>

                            <div x-show="showReopenForm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.self="showReopenForm = false">
                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Reabrir Tarea</h4>
                                    <form method="POST" action="{{ route('tasks.reopen', $task) }}">
                                        @csrf
                                        <div class="mb-4">
                                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Motivo de reapertura</label>
                                            <textarea id="reason" name="reason" rows="3" required
                                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                                placeholder="Indica el motivo por el cual se reabre esta tarea..."></textarea>
                                            <x-input-error for="reason" class="mt-2" />
                                        </div>
                                        <div class="flex items-center justify-end gap-3">
                                            <button type="button" @click="showReopenForm = false" class="btn-secondary text-sm">Cancelar</button>
                                            <button type="submit" class="btn-primary text-sm">Reabrir Tarea</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($canEdit)
                        <a href="{{ route('tasks.edit', $task) }}" class="btn-secondary text-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar Tarea
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
