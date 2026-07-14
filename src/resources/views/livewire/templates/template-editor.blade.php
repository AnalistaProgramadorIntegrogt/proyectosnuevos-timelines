<div class="space-y-6">
    {{-- Version Header --}}
    <div class="project-card bg-white dark:bg-surface-dark-card rounded-xl shadow-card border border-gray-100 dark:border-gray-700 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $template->name }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Versión {{ $version->version_number }}
                    <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $version->status === 'published' ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : '' }}
                        {{ $version->status === 'draft' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300' : '' }}
                        {{ $version->status === 'archived' ? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' : '' }}">
                        {{ ucfirst($version->status) }}
                    </span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="saveVersion" wire:loading.attr="disabled"
                    class="btn-secondary inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-surface-dark-card hover:bg-gray-50 dark:hover:bg-surface-dark-elevated transition shadow-soft">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Guardar Borrador
                </button>

                @if($version->status !== 'published')
                    <button wire:click="publishVersion" wire:loading.attr="disabled"
                        wire:confirm="¿Publicar esta versión? Los proyectos existentes usando versiones anteriores no se verán afectados."
                        class="btn-primary inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-primary-500 dark:bg-primary-600 text-white hover:bg-primary-600 dark:hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition shadow-soft">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Publicar Versión
                    </button>
                @endif
            </div>
        </div>

        {{-- Version Notes --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notas de la versión</label>
            <textarea wire:model="versionNotes" rows="2"
                class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 shadow-soft focus:border-primary-400 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                placeholder="Descripción de cambios en esta versión..."></textarea>
        </div>
    </div>

    {{-- Groups --}}
    <div class="space-y-4">
        @forelse($groups as $gIndex => $group)
            <div class="project-card bg-white dark:bg-surface-dark-card rounded-xl shadow-card border border-gray-100 dark:border-gray-700 overflow-hidden">
                {{-- Group Header --}}
                <div class="px-6 py-4 bg-surface dark:bg-surface-dark border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="flex items-center gap-1 text-gray-400 dark:text-gray-500">
                                <button wire:click="moveGroupUp({{ $gIndex }})" class="hover:text-gray-600 dark:hover:text-gray-300 transition" title="Mover arriba">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                </button>
                                <button wire:click="moveGroupDown({{ $gIndex }})" class="hover:text-gray-600 dark:hover:text-gray-300 transition" title="Mover abajo">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>

                            @if($editingGroupIndex === $gIndex)
                                <div class="flex-1 flex flex-wrap items-center gap-3">
                                    <input wire:model="groupName" type="text"
                                        class="flex-1 min-w-[200px] rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm shadow-soft focus:border-primary-400 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                        placeholder="Nombre del grupo..." />
                                    <label class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <input wire:model="groupIsGate" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-primary-500 shadow-sm focus:ring-primary-400 dark:bg-gray-700" />
                                        <span class="ml-2">Dependencia</span>
                                    </label>
                                    <button wire:click="saveGroup({{ $gIndex }})"
                                        class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 p-1 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/30 transition" title="Guardar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                    <button wire:click="cancelEditGroup"
                                        class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition" title="Cancelar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    <span class="text-sm font-semibold text-gray-400 dark:text-gray-500">#{{ $group['order'] }}</span>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $group['name'] }}</h4>
                                    @if($group['is_gate'] ?? false)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300">
                                            Dependencia
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if($editingGroupIndex !== $gIndex)
                            <div class="flex items-center gap-1 shrink-0">
                                <button wire:click="editGroup({{ $gIndex }})"
                                    class="text-gray-400 dark:text-gray-500 hover:text-primary-500 p-1.5 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/30 transition" title="Editar grupo">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="removeGroup({{ $gIndex }})"
                                    wire:confirm="¿Eliminar este grupo y todas sus tareas?"
                                    class="text-gray-400 dark:text-gray-500 hover:text-red-500 p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition" title="Eliminar grupo">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tasks inside group --}}
                <div class="p-6 space-y-3">
                    @forelse($group['tasks'] ?? [] as $tIndex => $task)
                        <div class="rounded-lg border border-gray-100 dark:border-gray-600 bg-surface dark:bg-surface-dark-elevated shadow-soft overflow-hidden">
                            {{-- Task Header --}}
                            <div class="px-4 py-3 flex items-center justify-between">
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    {{-- Reorder buttons --}}
                                    <div class="flex flex-col items-center gap-0.5 text-gray-400 dark:text-gray-500">
                                        <button wire:click="moveTaskUp({{ $gIndex }}, {{ $tIndex }})" class="hover:text-gray-600 dark:hover:text-gray-300 transition leading-none" title="Mover arriba">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                            </svg>
                                        </button>
                                        <button wire:click="moveTaskDown({{ $gIndex }}, {{ $tIndex }})" class="hover:text-gray-600 dark:hover:text-gray-300 transition leading-none" title="Mover abajo">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </div>

                                    @if($editingTaskGroupIndex === $gIndex && $editingTaskIndex === $tIndex)
                                        <div class="flex-1 space-y-2">
                                            <input wire:model="taskTitle" type="text"
                                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm shadow-soft focus:border-primary-400 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                                placeholder="Título de la tarea..." />
                                            <textarea wire:model="taskDescription" rows="2"
                                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm shadow-soft focus:border-primary-400 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                                placeholder="Descripción..."></textarea>
                                            <div class="flex flex-wrap items-center gap-3">
                                                <div>
                                                    <label class="text-xs text-gray-500 dark:text-gray-400">Duración (días)</label>
                                                    <input wire:model="taskDurationDays" type="number" min="1"
                                                        class="w-20 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm shadow-soft focus:border-primary-400 focus:ring focus:ring-primary-200 focus:ring-opacity-50" />
                                                </div>
                                                <label class="inline-flex items-center text-xs text-gray-600 dark:text-gray-400">
                                                    <input wire:model="taskIsRequired" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-primary-500 shadow-sm focus:ring-primary-400 dark:bg-gray-700" />
                                                    <span class="ml-1.5">Requerido</span>
                                                </label>
                                                <label class="inline-flex items-center text-xs text-gray-600 dark:text-gray-400">
                                                    <input wire:model="taskIsDeliverable" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-primary-500 shadow-sm focus:ring-primary-400 dark:bg-gray-700" />
                                                    <span class="ml-1.5">Entregable</span>
                                                </label>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-3">
                                                <div class="flex-1 min-w-[150px]">
                                                    <label class="text-xs text-gray-500 dark:text-gray-400">Responsables</label>
                                                    <select multiple wire:model="taskResponsibleUsers" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs shadow-soft h-20">
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="flex-1 min-w-[150px]">
                                                    <label class="text-xs text-gray-500 dark:text-gray-400">Aprobadores explícitos</label>
                                                    <select multiple wire:model="taskApproverUsers" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs shadow-soft h-20">
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1 pt-1">
                                                <button wire:click="saveTask({{ $gIndex }}, {{ $tIndex }})"
                                                    class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 p-1 rounded hover:bg-green-50 dark:hover:bg-green-900/30 transition" title="Guardar tarea">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                                <button wire:click="cancelEditTask"
                                                    class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-600 transition" title="Cancelar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-medium text-gray-400 dark:text-gray-500">#{{ $task['order'] }}</span>
                                                <span class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $task['title'] }}</span>
                                                @if($task['is_required'] ?? false)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">Req</span>
                                                @endif
                                                @if($task['is_deliverable'] ?? false)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300">Ent</span>
                                                @endif
                                            </div>
                                            @if($task['description'] ?? false)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-1">{{ $task['description'] }}</p>
                                            @endif
                                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $task['duration_days'] ?? '-' }} días</span>
                                        </div>
                                        <div class="flex items-center gap-1 shrink-0">
                                            <button wire:click="editTask({{ $gIndex }}, {{ $tIndex }})"
                                                class="text-gray-400 dark:text-gray-500 hover:text-primary-500 p-1.5 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/30 transition" title="Editar tarea">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button wire:click="removeTask({{ $gIndex }}, {{ $tIndex }})"
                                                wire:confirm="¿Eliminar esta tarea y sus subtareas?"
                                                class="text-gray-400 dark:text-gray-500 hover:text-red-500 p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition" title="Eliminar tarea">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Subtasks inside task --}}
                            <div class="px-4 pb-3 ml-6 space-y-1.5">
                                @foreach($task['subtasks'] ?? [] as $sIndex => $subtask)
                                    <div class="flex items-center gap-2 py-1 px-3 rounded-lg bg-white dark:bg-gray-700 border border-gray-50 dark:border-gray-600">
                                        {{-- Reorder --}}
                                        <div class="flex flex-col items-center gap-0.5 text-gray-400 dark:text-gray-500">
                                            <button wire:click="moveSubtaskUp({{ $gIndex }}, {{ $tIndex }}, {{ $sIndex }})" class="hover:text-gray-600 dark:hover:text-gray-300 transition leading-none" title="Mover arriba">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                                </svg>
                                            </button>
                                            <button wire:click="moveSubtaskDown({{ $gIndex }}, {{ $tIndex }}, {{ $sIndex }})" class="hover:text-gray-600 dark:hover:text-gray-300 transition leading-none" title="Mover abajo">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>
                                        </div>

                                        @if($editingSubtaskGroupIndex === $gIndex && $editingSubtaskTaskIndex === $tIndex && $editingSubtaskIndex === $sIndex)
                                            <div class="flex-1 flex flex-wrap items-center gap-2">
                                                <input wire:model="subtaskTitle" type="text"
                                                    class="flex-1 min-w-[120px] rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs shadow-soft focus:border-primary-400 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                                    placeholder="Título..." />
                                                <textarea wire:model="subtaskDescription" rows="1"
                                                    class="flex-1 min-w-[120px] rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs shadow-soft focus:border-primary-400 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                                    placeholder="Descripción..."></textarea>
                                                <input wire:model="subtaskDurationDays" type="number" min="1"
                                                    class="w-16 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs shadow-soft focus:border-primary-400 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                                    title="Días" />
                                                <label class="inline-flex items-center text-xs text-gray-600 dark:text-gray-400">
                                                    <input wire:model="subtaskIsDeliverable" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-primary-500 shadow-sm focus:ring-primary-400 dark:bg-gray-700" />
                                                    <span class="ml-1.5">Entregable</span>
                                                </label>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                                <div class="flex-1 min-w-[120px]">
                                                    <label class="text-xs text-gray-500 dark:text-gray-400">Responsables</label>
                                                    <select multiple wire:model="subtaskResponsibleUsers" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs shadow-soft h-16">
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="flex-1 min-w-[120px]">
                                                    <label class="text-xs text-gray-500 dark:text-gray-400">Aprobadores explícitos</label>
                                                    <select multiple wire:model="subtaskApproverUsers" class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs shadow-soft h-16">
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1 mt-2">
                                                <button wire:click="saveSubtask({{ $gIndex }}, {{ $tIndex }}, {{ $sIndex }})"
                                                    class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 p-1 rounded hover:bg-green-50 dark:hover:bg-green-900/30 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                                <button wire:click="cancelEditSubtask"
                                                    class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 dark:text-gray-500">#{{ $subtask['order'] }}</span>
                                            <span class="text-xs text-gray-700 dark:text-gray-300 flex-1 min-w-0 truncate">{{ $subtask['title'] }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-500 shrink-0">{{ $subtask['duration_days'] ?? '-' }}d</span>
                                            @if($subtask['is_deliverable'] ?? false)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300 shrink-0">Ent</span>
                                            @endif
                                            <button wire:click="editSubtask({{ $gIndex }}, {{ $tIndex }}, {{ $sIndex }})"
                                                class="text-gray-400 dark:text-gray-500 hover:text-primary-500 p-1 rounded hover:bg-primary-50 dark:hover:bg-primary-900/30 transition" title="Editar subtarea">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button wire:click="removeSubtask({{ $gIndex }}, {{ $tIndex }}, {{ $sIndex }})"
                                                wire:confirm="¿Eliminar esta subtarea?"
                                                class="text-gray-400 dark:text-gray-500 hover:text-red-500 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/30 transition" title="Eliminar subtarea">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach

                                {{-- Add Subtask --}}
                                @if(($editingTaskGroupIndex !== $gIndex || $editingTaskIndex !== $tIndex))
                                    <button wire:click="addSubtask({{ $gIndex }}, {{ $tIndex }})"
                                        class="inline-flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500 hover:text-primary-500 dark:hover:text-primary-400 py-1 px-2 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Añadir subtarea
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-3 italic">
                            Sin tareas en este grupo
                        </p>
                    @endforelse

                    {{-- Add Task --}}
                    @if($editingTaskGroupIndex !== $gIndex)
                        <div class="flex items-center gap-2 pt-2">
                            <input wire:model="taskTitle" type="text" placeholder="Título de nueva tarea..."
                                class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm shadow-soft focus:border-primary-400 focus:ring focus:ring-primary-200 focus:ring-opacity-50" />
                            <input wire:model="taskDurationDays" type="number" min="1" value="1"
                                class="w-16 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm shadow-soft focus:border-primary-400 focus:ring focus:ring-primary-200 focus:ring-opacity-50"
                                title="Días" />
                            <button wire:click="addTask({{ $gIndex }})"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg text-white bg-primary-500 hover:bg-primary-600 dark:bg-primary-600 dark:hover:bg-primary-500 transition shadow-soft">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Añadir
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white dark:bg-surface-dark-card rounded-xl shadow-card border border-gray-100 dark:border-gray-700">
                <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <p class="mt-4 text-gray-500 dark:text-gray-400">Esta plantilla no tiene grupos todavía.</p>
            </div>
        @endforelse
    </div>

    {{-- Add Group --}}
    <div class="project-card bg-white dark:bg-surface-dark-card rounded-xl shadow-card border border-dashed border-gray-200 dark:border-gray-600 p-6">
        <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3">Añadir nuevo grupo</h4>
        <div class="flex flex-wrap items-center gap-3">
            <input wire:model="groupName" type="text" placeholder="Nombre del grupo..."
                class="flex-1 min-w-[200px] rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 shadow-soft focus:border-primary-400 focus:ring focus:ring-primary-200 focus:ring-opacity-50" />
            <label class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400">
                <input wire:model="groupIsGate" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-primary-500 shadow-sm focus:ring-primary-400 dark:bg-gray-700" />
                <span class="ml-2">Dependencia</span>
            </label>
            <button wire:click="addGroup"
                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-xl text-white bg-primary-500 hover:bg-primary-600 dark:bg-primary-600 dark:hover:bg-primary-500 transition shadow-soft">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Añadir grupo
            </button>
        </div>
    </div>
</div>
