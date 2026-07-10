<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-text-primary">
                    {{ $project->name }}
                </h1>
                <p class="text-sm text-text-muted mt-0.5">Editar lista de tareas</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-text-muted mb-6">
                <a href="{{ route('projects.index') }}" class="hover:text-text-primary transition-colors">Proyectos</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <a href="{{ route('projects.show', $project) }}" class="hover:text-text-primary transition-colors">{{ $project->name }}</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-text-primary font-semibold">Editar Lista de Tareas</span>
            </nav>

            <!-- Info Banner -->
            <div class="project-card mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-text-muted">
                            Arrastra las filas para reordenar las tareas. Haz clic en los campos para editarlos.
                        </p>
                    </div>
                    <a href="{{ route('projects.show', $project) }}" class="btn-secondary text-xs">
                        ← Volver al timeline
                    </a>
                </div>
            </div>

            <!-- Add Group button -->
            <div class="flex justify-end mb-6">
                <button onclick="openAddGroupModal()" class="btn-primary text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Añadir Grupo
                </button>
            </div>

            @forelse($project->groups as $group)
                @php
                    $groupStatusColors = [
                        'active' => 'border-l-[var(--integro-red)]',
                        'locked' => 'border-l-[var(--integro-gray)]',
                        'completed' => 'border-l-[var(--success)]',
                    ];
                    $borderColor = $groupStatusColors[$group->status] ?? 'border-l-[var(--border-soft)]';
                    $isLocked = $group->status === 'locked';
                @endphp

                <!-- Between-group + Add Group divider -->
                @if(!$loop->first)
                    <div class="flex items-center justify-center my-4">
                        <div class="flex-1 h-px bg-[var(--border-soft)]"></div>
                        <button onclick="openAddGroupModal({{ $group->order }})"
                                class="mx-4 w-8 h-8 rounded-full border-2 border-[var(--border-soft)] text-[var(--integro-gray-text)] hover:border-[var(--integro-red)] hover:text-[var(--integro-red)] transition-all flex items-center justify-center flex-shrink-0"
                                title="Añadir grupo aquí">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                        <div class="flex-1 h-px bg-[var(--border-soft)]"></div>
                    </div>
                @endif

                <!-- Group Container Card -->
                <div class="project-card mb-6 relative overflow-visible border-l-4 {{ $borderColor }} {{ $isLocked ? 'opacity-60' : '' }}">
                    <!-- Group Header -->
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-[var(--border-soft)]"
                         x-data="groupEditor({{ $group->id }}, '{{ addslashes($group->name) }}', {{ $group->is_gate ? 'true' : 'false' }}, '{{ $group->status }}')">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <!-- Group name - inline editable -->
                            <template x-if="!editing">
                                <h3 class="text-base font-bold text-text-primary truncate cursor-pointer hover:bg-[var(--surface-muted)] px-2 py-1 rounded transition-colors"
                                    @click="startEdit()"
                                    x-text="name"
                                    title="Clic para editar">{{ $group->name }}</h3>
                            </template>
                            <template x-if="editing">
                                <input x-ref="nameInput" type="text" x-model="name"
                                       class="flex-1 text-base font-bold border border-[var(--integro-red)] rounded px-2 py-1 bg-surface text-text-primary focus:ring-2 focus:ring-[var(--integro-red)]"
                                       @blur="save()"
                                       @keydown.enter="save()"
                                       @keydown.escape="cancelEdit()"
                                       maxlength="255">
                            </template>

                            <!-- Status badge -->
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($group->status === 'active') bg-amber-50 text-amber-700 border border-amber-200
                                @elseif($group->status === 'locked') bg-gray-100 text-gray-600 border border-gray-200
                                @elseif($group->status === 'completed') bg-green-50 text-green-700 border border-green-200
                                @else bg-gray-100 text-gray-600 border border-gray-200
                                @endif">
                                @if($group->status === 'active') Activo
                                @elseif($group->status === 'locked') Bloqueado
                                @elseif($group->status === 'completed') Completado
                                @else {{ $group->status }}
                                @endif
                            </span>

                            <!-- is_gate toggle -->
                            <label class="inline-flex items-center gap-1.5 text-xs text-text-muted cursor-pointer select-none"
                                   title="Este grupo tiene una dependencia de aprobación">
                                <input type="checkbox" x-model="isGate" @change.debounce.500ms="save()"
                                       class="rounded border-[var(--border-soft)] text-[var(--integro-red)] focus:ring-[var(--integro-red)] h-3.5 w-3.5">
                                <span class="font-medium">Dependencia</span>
                            </label>
                        </div>

                        <!-- Right: Save + Delete buttons -->
                        <div class="flex items-center gap-2 flex-shrink-0 ml-3">
                            <button @click="save()"
                                    class="btn-primary text-xs !px-3 !py-1.5"
                                    :disabled="saving"
                                    x-text="saving ? 'Guardando...' : 'Guardar'">
                                Guardar
                            </button>
                            <button onclick="confirmDeleteGroup({{ $group->id }}, '{{ addslashes($group->name) }}')"
                                    class="p-1.5 rounded text-[var(--integro-gray-text)] hover:text-[var(--integro-red)] hover:bg-red-50 transition-colors"
                                    title="Eliminar grupo">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Task Table -->
                    @if($group->tasks->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-[var(--border-soft)] bg-[var(--surface-muted)]">
                                        <th class="px-3 py-3 text-left text-xs font-medium text-text-muted tracking-wider w-10">#</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-text-muted tracking-wider w-10">Arrastrar</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-text-muted tracking-wider">Tarea</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-text-muted tracking-wider w-24">Estado</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-text-muted tracking-wider w-24">Tipo</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-text-muted tracking-wider w-44">Responsable</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-text-muted tracking-wider w-28">Inicio</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-text-muted tracking-wider w-28">Fin Calc.</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-text-muted tracking-wider w-28">Fin Real</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-text-muted tracking-wider w-20">Duración</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-text-muted tracking-wider w-20">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable-tbody-{{ $group->id }}" class="divide-y divide-[var(--border-soft)]">
                                    @foreach($group->tasks as $task)
                                        <tr class="task-row hover:bg-[var(--surface-muted)] transition-colors" data-task-id="{{ $task->id }}" data-group-id="{{ $group->id }}" data-order="{{ $task->order }}">
                                            <td class="px-3 py-2 text-text-muted text-xs font-mono whitespace-nowrap">
                                                {{ $task->order }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <div class="drag-handle cursor-grab active:cursor-grabbing text-[var(--integro-gray-text)]/50 hover:text-[var(--integro-gray)] transition-colors inline-flex">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <circle cx="9" cy="5" r="1.5"/>
                                                        <circle cx="15" cy="5" r="1.5"/>
                                                        <circle cx="9" cy="12" r="1.5"/>
                                                        <circle cx="15" cy="12" r="1.5"/>
                                                        <circle cx="9" cy="19" r="1.5"/>
                                                        <circle cx="15" cy="19" r="1.5"/>
                                                    </svg>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap min-w-[200px]">
                                                <div x-data="{ editing: false, title: '{{ addslashes($task->title) }}' }" class="group relative">
                                                    <template x-if="!editing">
                                                        <div class="flex items-center">
                                                            <span x-text="title" class="text-text-primary text-sm font-medium cursor-pointer hover:bg-[var(--surface-muted)] px-1.5 py-0.5 rounded transition-colors"
                                                                  @click="editing = true; $nextTick(() => $refs.titleInput.focus())"
                                                                  :title="'Clic para editar'"></span>
                                                            @if($task->is_required)
                                                                <svg class="w-3.5 h-3.5 ml-1.5 text-[var(--integro-red)]/60 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                </svg>
                                                            @endif
                                                        </div>
                                                    </template>
                                                    <template x-if="editing">
                                                        <input x-ref="titleInput" type="text" x-model="title"
                                                               class="w-full border border-[var(--integro-red)] rounded px-2 py-1 text-sm bg-surface text-text-primary focus:ring-2 focus:ring-[var(--integro-red)]"
                                                               @blur="editing = false; saveTask({{ $task->id }}, 'title', title)"
                                                               @keydown.enter="$event.target.blur()"
                                                               @keydown.escape="title = '{{ addslashes($task->title) }}'; editing = false">
                                                    </template>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                    @if($task->status === 'en_proceso') status-en-proceso
                                                    @elseif($task->status === 'entregado') status-entregado
                                                    @elseif($task->status === 'aprobado') status-aprobado
                                                    @elseif($task->status === 'atrasado') status-atrasado
                                                    @elseif($task->status === 'rechazado') status-rechazado
                                                    @elseif($task->status === 'pending') status-pending
                                                    @else bg-gray-100 text-gray-700 border border-gray-200
                                                    @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status ?? 'pendiente')) }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <span onclick="toggleDeliverable({{ $task->id }}, {{ $task->is_deliverable ? 'false' : 'true' }})"
                                                      class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium cursor-pointer transition-all duration-150
                                                      @if($task->is_deliverable)
                                                          bg-[var(--integro-red)]/10 text-[var(--integro-red)] border border-[var(--integro-red)]/30 hover:bg-[var(--integro-red)]/20
                                                      @else
                                                          bg-gray-100 text-gray-500 border border-gray-200 hover:bg-gray-200
                                                      @endif"
                                                      title="Clic para cambiar tipo">
                                                    @if($task->is_deliverable)
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <span>Entregable</span>
                                                    @else
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span>No Entregable</span>
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <select onchange="saveTask({{ $task->id }}, 'responsible_user_id', this.value)"
                                                        class="text-xs border border-[var(--border-soft)] rounded px-2 py-1.5 bg-surface text-text-primary focus:ring-2 focus:ring-[var(--integro-red)] w-full max-w-[11rem]">
                                                    <option value="">— Sin asignar —</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}" @if($task->responsible_user_id === $user->id) selected @endif>
                                                            {{ $user->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-text-muted start-date-cell">
                                                {{ $task->calculated_start_date ? $task->calculated_start_date->format('d/m/Y') : '—' }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-sm text-text-muted calc-end-date-cell">
                                                {{ $task->calculated_end_date ? $task->calculated_end_date->format('d/m/Y') : '—' }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                @php
                                                    $realEndDate = $task->real_end_date ? $task->real_end_date->format('Y-m-d') : '';
                                                    $calcEndDate = $task->calculated_end_date ? $task->calculated_end_date->format('d/m/Y') : '—';
                                                @endphp
                                                <input type="date" value="{{ $realEndDate }}"
                                                       onchange="previewTimeline(this); saveTask({{ $task->id }}, 'real_end_date', this.value || null)"
                                                       class="real-end-date-input text-xs border border-[var(--border-soft)] rounded px-2 py-1.5 bg-surface text-text-primary focus:ring-2 focus:ring-[var(--integro-red)] w-full">
                                                <span class="calc-hint text-xs text-text-muted italic block mt-0.5 {{ $task->real_end_date ? 'hidden' : '' }}">
                                                    Calc: {{ $calcEndDate }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <input type="number" min="1" max="30" value="{{ $task->duration_days }}"
                                                       oninput="previewTimeline(this)"
                                                       onchange="saveTask({{ $task->id }}, 'duration_days', this.value)"
                                                       class="duration-input w-16 text-xs border border-[var(--border-soft)] rounded px-2 py-1.5 bg-surface text-text-primary focus:ring-2 focus:ring-[var(--integro-red)] text-center">
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap">
                                                <button onclick="confirmDeleteTask({{ $task->id }}, '{{ addslashes($task->title) }}')"
                                                        class="p-1.5 rounded text-[var(--integro-gray-text)] hover:text-[var(--integro-red)] hover:bg-red-50 transition-colors"
                                                        title="Eliminar tarea">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                        <!-- "+" divider between tasks -->
                                        <tr class="insert-row" data-group-id="{{ $group->id }}" data-insert-after="{{ $task->order }}">
                                            <td colspan="11" class="px-0 py-0 relative">
                                                <div class="insert-divider group h-8 flex items-center justify-center cursor-pointer transition-colors hover:bg-[var(--surface-muted)]"
                                                     onclick="openInsertModal({{ $group->id }}, {{ $task->order + 1 }})">
                                                    <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 h-px bg-[var(--border-soft)] group-hover:bg-[var(--integro-red)]/50 transition-colors"></div>
                                                    <div class="relative z-10 flex items-center justify-center">
                                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[var(--integro-gray)] text-white group-hover:bg-[var(--integro-red)] group-hover:w-7 group-hover:h-7 group-hover:scale-110 transition-all duration-150">
                                                            <svg class="w-3 h-3 group-hover:w-4 group-hover:h-4 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- + Add Task at bottom of group -->
                        <div class="mt-3 flex justify-center">
                            <button onclick="openInsertModal({{ $group->id }}, {{ ($group->tasks->max('order') ?? 0) + 1 }})"
                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-[var(--integro-red)] hover:text-[var(--integro-red-hover)] hover:bg-red-50 px-3 py-1.5 rounded transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Añadir tarea
                            </button>
                        </div>
                    @else
                        <!-- Empty group -->
                        <div class="text-center py-8">
                            <svg class="w-10 h-10 mx-auto text-[var(--integro-gray-text)]/40 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-sm text-text-muted mb-3">Este grupo no tiene tareas aún.</p>
                            <button onclick="openInsertModal({{ $group->id }}, 1)" class="btn-primary text-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Añadir primera tarea
                            </button>
                        </div>
                    @endif
                </div>
            @empty
                <!-- No groups at all -->
                <div class="project-card">
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 mx-auto text-[var(--integro-gray-text)]/40 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-sm text-text-muted mb-2">No hay grupos en este proyecto.</p>
                        <p class="text-xs text-text-muted mb-6">Crea un grupo para empezar a organizar las tareas.</p>
                        <button onclick="openAddGroupModal()" class="btn-primary text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Crear primer grupo
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- ==================== MODALS ==================== -->

    <!-- Add Group Modal -->
    <div id="addGroupModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="fixed inset-0 bg-black/60 transition-opacity" onclick="closeAddGroupModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-surface border border-[var(--border-soft)] shadow-dialog w-full max-w-lg">
                <div class="px-6 py-5 border-b border-[var(--border-soft)] flex items-center justify-between">
                    <h3 class="text-base font-bold text-text-primary">Nuevo Grupo</h3>
                    <button onclick="closeAddGroupModal()" class="text-text-muted hover:text-text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="addGroupForm" method="POST" class="p-6 space-y-4" action="{{ route('projects.groups.store', $project) }}">
                    @csrf
                    <input type="hidden" name="insert_after_order" id="addGroupAfterOrder" value="">

                    <div>
                        <label for="addGroupName" class="block text-sm font-semibold text-text-primary mb-1">Nombre del grupo *</label>
                        <input type="text" name="name" id="addGroupName" required maxlength="255"
                               class="w-full border border-[var(--border-soft)] rounded px-3 py-2 bg-surface text-text-primary focus:ring-2 focus:ring-[var(--integro-red)]"
                               placeholder="Ej: Planificación, Ejecución, Cierre">
                        <x-input-error for="name" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-text-primary mb-2">Posición</label>
                        <select id="addGroupPosition" onchange="document.getElementById('addGroupAfterOrder').value = this.value"
                                class="w-full border border-[var(--border-soft)] rounded px-3 py-2 bg-surface text-text-primary focus:ring-2 focus:ring-[var(--integro-red)]">
                            <option value="">— Al final —</option>
                            @foreach($project->groups as $g)
                                <option value="{{ $g->order }}">Después de: {{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="inline-flex items-center gap-2 text-sm text-text-primary cursor-pointer">
                            <input type="checkbox" name="is_gate" value="1"
                                   class="rounded border-[var(--border-soft)] text-[var(--integro-red)] focus:ring-[var(--integro-red)]">
                            Dependencia
                        </label>
                        <p class="text-xs text-text-muted mt-1 ml-6">Los grupos con dependencia requieren una decisión de aprobación antes de continuar.</p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" onclick="closeAddGroupModal()" class="btn-secondary text-sm">Cancelar</button>
                        <button type="submit" class="btn-primary text-sm">Crear Grupo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Insert Task Modal -->
    <div id="insertModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="fixed inset-0 bg-black/60 transition-opacity" onclick="closeInsertModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-surface border border-[var(--border-soft)] shadow-dialog w-full max-w-lg">
                <div class="px-6 py-5 border-b border-[var(--border-soft)] flex items-center justify-between">
                    <h3 class="text-base font-bold text-text-primary">Nueva Tarea</h3>
                    <button onclick="closeInsertModal()" class="text-text-muted hover:text-text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="insertTaskForm" method="POST" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="order" id="insertOrder" value="">

                    <div>
                        <label for="insertTitle" class="block text-sm font-semibold text-text-primary mb-1">Título *</label>
                        <input type="text" name="title" id="insertTitle" required maxlength="255"
                               class="w-full border border-[var(--border-soft)] rounded px-3 py-2 bg-surface text-text-primary focus:ring-2 focus:ring-[var(--integro-red)]"
                               placeholder="Nombre de la tarea">
                        <x-input-error for="title" class="mt-1" />
                    </div>

                    <div>
                        <label for="insertDescription" class="block text-sm font-semibold text-text-primary mb-1">Descripción</label>
                        <textarea name="description" id="insertDescription" rows="2"
                                  class="w-full border border-[var(--border-soft)] rounded px-3 py-2 bg-surface text-text-primary focus:ring-2 focus:ring-[var(--integro-red)]"
                                  placeholder="Descripción opcional"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="insertDuration" class="block text-sm font-semibold text-text-primary mb-1">Duración (días) *</label>
                            <input type="number" name="duration_days" id="insertDuration" required min="1" max="30" value="3"
                                   class="w-full border border-[var(--border-soft)] rounded px-3 py-2 bg-surface text-text-primary focus:ring-2 focus:ring-[var(--integro-red)]">
                            <x-input-error for="duration_days" class="mt-1" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-text-primary mb-1">Opciones</label>
                            <div class="flex items-center gap-4 mt-2">
                                <label class="inline-flex items-center gap-2 text-sm text-text-primary">
                                    <input type="checkbox" name="is_required" value="1" checked
                                           class="rounded border-[var(--border-soft)] text-[var(--integro-red)] focus:ring-[var(--integro-red)]">
                                    Requerida
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm text-text-primary">
                                    <input type="checkbox" name="is_deliverable" value="1"
                                           class="rounded border-[var(--border-soft)] text-[var(--integro-red)] focus:ring-[var(--integro-red)]">
                                    Entregable
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" onclick="closeInsertModal()" class="btn-secondary text-sm">Cancelar</button>
                        <button type="submit" class="btn-primary text-sm">Crear Tarea</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Task Modal -->
    <div id="deleteTaskModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="fixed inset-0 bg-black/60 transition-opacity" onclick="closeDeleteTaskModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-surface border border-[var(--border-soft)] shadow-dialog w-full max-w-md">
                <div class="px-6 py-5 border-b border-[var(--border-soft)]">
                    <h3 class="text-base font-bold text-text-primary">Eliminar Tarea</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-text-muted mb-2">
                        ¿Estás seguro de que deseas eliminar la tarea <strong class="text-text-primary" id="deleteTaskTitle"></strong>?
                    </p>
                    <p class="text-xs text-text-muted">
                        Esta acción no se puede deshacer. También se eliminarán las subtareas, entregables y versiones asociadas.
                    </p>
                </div>
                <div class="px-6 py-4 border-t border-[var(--border-soft)] flex items-center justify-end gap-3">
                    <button onclick="closeDeleteTaskModal()" class="btn-secondary text-sm">Cancelar</button>
                    <form id="deleteTaskForm" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger text-sm">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Group Modal -->
    <div id="deleteGroupModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="fixed inset-0 bg-black/60 transition-opacity" onclick="closeDeleteGroupModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-surface border border-[var(--border-soft)] shadow-dialog w-full max-w-md">
                <div class="px-6 py-5 border-b border-[var(--border-soft)]">
                    <h3 class="text-base font-bold text-text-primary">Eliminar Grupo</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-text-muted mb-2">
                        ¿Estás seguro de que deseas eliminar el grupo <strong class="text-text-primary" id="deleteGroupName"></strong>?
                    </p>
                    <p class="text-xs text-text-muted">
                        Esta acción eliminará TODAS las tareas del grupo y no se puede deshacer.
                        Las tareas con responsables asignados no permitirán la eliminación.
                    </p>
                </div>
                <div class="px-6 py-4 border-t border-[var(--border-soft)] flex items-center justify-end gap-3">
                    <button onclick="closeDeleteGroupModal()" class="btn-secondary text-sm">Cancelar</button>
                    <form id="deleteGroupForm" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger text-sm">Eliminar Grupo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        // CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        // =====================================================================
        // Alpine.js group editor component
        // =====================================================================
        document.addEventListener('alpine:init', () => {
            Alpine.data('groupEditor', (groupId, initialName, initialIsGate, initialStatus) => ({
                groupId: groupId,
                name: initialName,
                isGate: initialIsGate,
                status: initialStatus,
                editing: false,
                saving: false,
                originalName: initialName,
                originalIsGate: initialIsGate,
                originalStatus: initialStatus,

                startEdit() {
                    this.originalName = this.name;
                    this.editing = true;
                    this.$nextTick(() => {
                        if (this.$refs.nameInput) {
                            this.$refs.nameInput.focus();
                            this.$refs.nameInput.select();
                        }
                    });
                },

                cancelEdit() {
                    this.name = this.originalName;
                    this.editing = false;
                },

                save() {
                    if (this.saving) return;
                    this.saving = true;

                    const payload = {
                        name: this.name,
                        is_gate: this.isGate,
                        status: this.status,
                    };

                    fetch('/projects/groups/' + this.groupId, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.editing = false;
                            this.originalName = this.name;
                            window.location.reload();
                        } else {
                            alert('Error al guardar el grupo.');
                        }
                    })
                    .catch(error => {
                        console.error('Error saving group:', error);
                        alert('Error al guardar el grupo. Por favor intenta de nuevo.');
                    })
                    .finally(() => {
                        this.saving = false;
                    });
                },
            }));
        });

        // =====================================================================
        // Save Task (AJAX)
        // =====================================================================
        function toggleDeliverable(taskId, newValue) {
            saveTask(taskId, 'is_deliverable', newValue);
        }

        function saveTask(taskId, field, value) {
            const payload = { [field]: value };
            if (value === null || value === '') {
                if (field === 'real_end_date') {
                    payload[field] = null;
                }
                if (field === 'responsible_user_id') {
                    payload[field] = null;
                }
            }

            fetch('/projects/tasks/' + taskId, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            })
            .then(response => response.json())
            .then(data => {
                // Background save successful, no need to reload since preview is active
                if (data.success) {
                    // console.log('Autosaved successfully');
                }
            })
            .catch(error => {
                console.error('Error saving task:', error);
                alert('Error al guardar en segundo plano. Por favor, recarga la página.');
            });
        }

        // =====================================================================
        // Live Preview Timeline
        // =====================================================================
        const projectStartDateStr = '{{ $project->start_date }}';

        function parseDate(dateStr) {
            if (!dateStr) return null;
            const parts = dateStr.split('-');
            if (parts.length === 3) {
                return new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            }
            return new Date(dateStr);
        }

        function formatDate(dateObj) {
            if (!dateObj) return '—';
            const d = dateObj.getDate().toString().padStart(2, '0');
            const m = (dateObj.getMonth() + 1).toString().padStart(2, '0');
            const y = dateObj.getFullYear();
            return `${d}/${m}/${y}`;
        }

        function isAfter(date1, date2) {
            if (!date1 || !date2) return false;
            // set to midnight to compare just dates
            const d1 = new Date(date1.getFullYear(), date1.getMonth(), date1.getDate());
            const d2 = new Date(date2.getFullYear(), date2.getMonth(), date2.getDate());
            return d1.getTime() > d2.getTime();
        }

        function previewTimeline() {
            let currentDate = parseDate(projectStartDateStr);
            const rows = document.querySelectorAll('tr.task-row');

            rows.forEach(row => {
                const durationInput = row.querySelector('.duration-input');
                const realEndDateInput = row.querySelector('.real-end-date-input');
                const startCell = row.querySelector('.start-date-cell');
                const endCalcCell = row.querySelector('.calc-end-date-cell');
                const calcHintSpan = row.querySelector('.calc-hint');

                if (!durationInput || !startCell || !endCalcCell) return;

                const duration = parseInt(durationInput.value) || 1;
                
                const calculatedStartDate = new Date(currentDate.getTime());
                
                const calculatedEndDate = new Date(currentDate.getTime());
                calculatedEndDate.setDate(calculatedEndDate.getDate() + (duration - 1));

                startCell.textContent = formatDate(calculatedStartDate);
                endCalcCell.textContent = formatDate(calculatedEndDate);
                
                const realEndDateVal = realEndDateInput ? realEndDateInput.value : null;
                
                if (calcHintSpan) {
                    calcHintSpan.textContent = 'Calc: ' + formatDate(calculatedEndDate);
                    if (realEndDateVal) {
                        calcHintSpan.classList.add('hidden');
                    } else {
                        calcHintSpan.classList.remove('hidden');
                    }
                }

                let realEndDateObj = realEndDateVal ? parseDate(realEndDateVal) : null;

                if (realEndDateObj && isAfter(realEndDateObj, calculatedEndDate)) {
                    currentDate = new Date(realEndDateObj.getTime());
                    currentDate.setDate(currentDate.getDate() + 1);
                } else {
                    currentDate = new Date(calculatedEndDate.getTime());
                    currentDate.setDate(currentDate.getDate() + 1);
                }
            });
        }

        // =====================================================================
        // Add Group Modal
        // =====================================================================
        function openAddGroupModal(afterOrder) {
            const modal = document.getElementById('addGroupModal');
            const positionSelect = document.getElementById('addGroupPosition');
            const afterInput = document.getElementById('addGroupAfterOrder');

            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');

            if (afterOrder !== undefined) {
                afterInput.value = afterOrder;
                Array.from(positionSelect.options).forEach(opt => {
                    if (opt.value === String(afterOrder)) {
                        opt.selected = true;
                    }
                });
            } else {
                afterInput.value = '';
                positionSelect.value = '';
            }

            document.getElementById('addGroupName').focus();
        }

        function closeAddGroupModal() {
            document.getElementById('addGroupModal').classList.add('hidden');
            document.getElementById('addGroupModal').setAttribute('aria-hidden', 'true');
        }

        // =====================================================================
        // Insert Task Modal
        // =====================================================================
        function openInsertModal(groupId, order) {
            document.getElementById('insertModal').classList.remove('hidden');
            document.getElementById('insertModal').setAttribute('aria-hidden', 'false');
            document.getElementById('insertOrder').value = order;
            document.getElementById('insertTaskForm').action = '/projects/groups/' + groupId + '/tasks';
            document.getElementById('insertTitle').focus();
        }

        function closeInsertModal() {
            document.getElementById('insertModal').classList.add('hidden');
            document.getElementById('insertModal').setAttribute('aria-hidden', 'true');
        }

        // =====================================================================
        // Delete Task
        // =====================================================================
        function confirmDeleteTask(taskId, title) {
            document.getElementById('deleteTaskTitle').textContent = title;
            document.getElementById('deleteTaskForm').action = '/tasks/' + taskId;
            document.getElementById('deleteTaskModal').classList.remove('hidden');
            document.getElementById('deleteTaskModal').setAttribute('aria-hidden', 'false');
        }

        function closeDeleteTaskModal() {
            document.getElementById('deleteTaskModal').classList.add('hidden');
            document.getElementById('deleteTaskModal').setAttribute('aria-hidden', 'true');
        }

        // =====================================================================
        // Delete Group
        // =====================================================================
        function confirmDeleteGroup(groupId, name) {
            document.getElementById('deleteGroupName').textContent = name;
            document.getElementById('deleteGroupForm').action = '/projects/groups/' + groupId;
            document.getElementById('deleteGroupModal').classList.remove('hidden');
            document.getElementById('deleteGroupModal').setAttribute('aria-hidden', 'false');
        }

        function closeDeleteGroupModal() {
            document.getElementById('deleteGroupModal').classList.add('hidden');
            document.getElementById('deleteGroupModal').setAttribute('aria-hidden', 'true');
        }

        // =====================================================================
        // Close modals on Escape key
        // =====================================================================
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddGroupModal();
                closeInsertModal();
                closeDeleteTaskModal();
                closeDeleteGroupModal();
            }
        });

        // =====================================================================
        // SortableJS Drag & Drop (per group)
        // =====================================================================
        document.addEventListener('DOMContentLoaded', function() {
            const sortableBodies = document.querySelectorAll('[id^="sortable-tbody-"]');

            sortableBodies.forEach(function(tbody) {
                new Sortable(tbody, {
                    handle: '.drag-handle',
                    animation: 200,
                    easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)',
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                    filter: '.insert-row',
                    onStart: function(evt) {
                        if (evt.item.classList.contains('insert-row')) {
                            evt.preventDefault();
                        }
                    },
                    onEnd: function(evt) {
                        const rows = tbody.querySelectorAll('tr.task-row');
                        const tasks = [];
                        let order = 1;

                        rows.forEach(function(row) {
                            const taskId = row.getAttribute('data-task-id');
                            if (taskId) {
                                tasks.push({
                                    id: parseInt(taskId),
                                    order: order++,
                                });
                            }
                        });

                        if (tasks.length === 0) return;

                        const projectId = '{{ $project->id }}';

                        fetch('/projects/' + projectId + '/tasks/reorder', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ tasks: tasks }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Error reordering tasks:', error);
                            alert('Error al reordenar. Por favor intenta de nuevo.');
                        });
                    },
                });
            });

            // Add sortable styles
            const style = document.createElement('style');
            style.textContent = `
                .sortable-ghost {
                    opacity: 0.4;
                    background: #f0f0f1 !important;
                    outline: 2px dashed var(--integro-gray);
                }
                .sortable-drag {
                    opacity: 0.9;
                    background: white !important;
                    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);
                }
                .dark .sortable-ghost {
                    background: #2d2d2e !important;
                    outline-color: var(--integro-gray);
                }
                .dark .sortable-drag {
                    background: #1a1a1a !important;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
    @endpush
</x-app-layout>
