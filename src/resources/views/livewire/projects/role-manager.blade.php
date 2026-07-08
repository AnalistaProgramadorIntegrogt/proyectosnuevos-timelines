<div class="project-card p-6 space-y-6" x-data="{ createForm: false }">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold dark:text-white">Roles del Proyecto</h3>
        <button
            type="button"
            x-on:click="createForm = !createForm"
            class="btn-primary inline-flex items-center gap-2 text-sm"
        >
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Nuevo Rol
        </button>
    </div>

    <!-- Create Role Form -->
    <div x-show="createForm" x-cloak class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 space-y-4">
        <h4 class="font-medium dark:text-white">Crear Nuevo Rol</h4>

        <div>
            <label for="newRoleName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre del Rol</label>
            <input
                id="newRoleName"
                type="text"
                wire:model="newRoleName"
                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Ej: Diseñador, Revisor, Aprobador..."
            />
            @error('newRoleName')
                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Permissions Grid -->
        <div>
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Permisos</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach([
                    'can_manage_settings' => 'Gestionar configuración',
                    'can_manage_roles' => 'Gestionar roles',
                    'can_add_edit_tasks' => 'Agregar/editar tareas',
                    'can_reorder_tasks' => 'Reordenar tareas',
                    'can_upload_deliverables' => 'Subir entregables',
                    'can_approve_tasks' => 'Aprobar tareas',
                    'can_edit_status' => 'Editar estado',
                    'can_view_audit' => 'Ver auditoría',
                ] as $field => $label)
                    <label class="flex items-center gap-2 p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer">
                        <input
                            type="checkbox"
                            wire:model="newRolePermissions.{{ $field }}"
                            class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        />
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <button
                type="button"
                x-on:click="createForm = false"
                class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
            >
                Cancelar
            </button>
            <button
                type="button"
                wire:click="createRole"
                wire:loading.attr="disabled"
                class="btn-primary inline-flex items-center gap-2 text-sm disabled:opacity-50"
            >
                <span wire:loading.remove>Crear Rol</span>
                <span wire:loading>Creando...</span>
            </button>
        </div>
    </div>

    <!-- Roles List -->
    @if($roles->isNotEmpty())
        <div class="space-y-4">
            @foreach($roles as $role)
                @if($editRoleId === $role->id)
                    <!-- Edit Form -->
                    <div class="border border-indigo-300 dark:border-indigo-600 rounded-lg p-4 space-y-4 bg-indigo-50 dark:bg-indigo-900/10">
                        <div>
                            <label for="editRoleName_{{ $role->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre del Rol</label>
                            <input
                                id="editRoleName_{{ $role->id }}"
                                type="text"
                                wire:model="editRoleName"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            @error('editRoleName')
                                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Permisos</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach([
                                    'can_manage_settings' => 'Gestionar configuración',
                                    'can_manage_roles' => 'Gestionar roles',
                                    'can_add_edit_tasks' => 'Agregar/editar tareas',
                                    'can_reorder_tasks' => 'Reordenar tareas',
                                    'can_upload_deliverables' => 'Subir entregables',
                                    'can_approve_tasks' => 'Aprobar tareas',
                                    'can_edit_status' => 'Editar estado',
                                    'can_view_audit' => 'Ver auditoría',
                                ] as $field => $label)
                                    <label class="flex items-center gap-2 p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700/50 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            wire:model="editRolePermissions.{{ $field }}"
                                            class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Task Visibility Rules -->
                        <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                            <button
                                type="button"
                                wire:click="toggleVisibilityConfig"
                                class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Visibilidad por tarea
                                <svg class="w-3 h-3 transition-transform duration-200" x-bind:class="{ 'rotate-180': \$wire.showVisibilityConfig }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="$wire.showVisibilityConfig" x-cloak class="mt-3 space-y-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Define qué tareas y grupos puede ver y editar este rol. Por defecto todo es visible.</p>
                                @if(count($groups) > 0)
                                    <div class="flex items-center gap-2 flex-wrap mb-3">
                                        <button type="button" wire:click="selectAllView" class="px-2 py-1 text-xs rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-800/50 transition-colors">Todo Ver</button>
                                        <button type="button" wire:click="deselectAllView" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Ninguno Ver</button>
                                        <button type="button" wire:click="selectAllEdit" class="px-2 py-1 text-xs rounded bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-800/50 transition-colors">Todo Editar</button>
                                        <button type="button" wire:click="deselectAllEdit" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Ninguno Editar</button>
                                    </div>
                                    <div class="space-y-4 max-h-96 overflow-y-auto">
                                        @foreach($groups as $groupIdx => $group)
                                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $group['name'] }}</span>
                                                        @if($group['is_gate'])
                                                            <span class="text-xs px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Dependencia</span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        <label class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 cursor-pointer">
                                                            <input type="checkbox" wire:model="editVisibilityRules.group_{{ $group['id'] }}.can_view" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-3 h-3" />
                                                            Ver
                                                        </label>
                                                        <label class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 cursor-pointer">
                                                            <input type="checkbox" wire:model="editVisibilityRules.group_{{ $group['id'] }}.can_edit" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-3 h-3" />
                                                            Editar
                                                        </label>
                                                    </div>
                                                </div>
                                                @if(count($group['tasks']) > 0)
                                                    <div class="mt-2 ml-4 space-y-1.5">
                                                        @foreach($group['tasks'] as $task)
                                                            <div class="flex items-center justify-between py-1 px-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $task['title'] }}</span>
                                                                <div class="flex items-center gap-3">
                                                                    <label class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 cursor-pointer">
                                                                        <input type="checkbox" wire:model="editVisibilityRules.task_{{ $task['id'] }}.can_view" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-3 h-3" />
                                                                        Ver
                                                                    </label>
                                                                    <label class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 cursor-pointer">
                                                                        <input type="checkbox" wire:model="editVisibilityRules.task_{{ $task['id'] }}.can_edit" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-3 h-3" />
                                                                        Editar
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-xs text-gray-400 dark:text-gray-500 italic">No hay grupos ni tareas en este proyecto. Crea grupos y tareas primero.</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2">
                            <button
                                type="button"
                                wire:click="cancelEdit"
                                class="px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-xs font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                            >
                                Cancelar
                            </button>
                            <button
                                type="button"
                                wire:click="updateRole"
                                wire:loading.attr="disabled"
                                class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white text-xs font-medium rounded-lg transition-colors disabled:opacity-50"
                            >
                                <span wire:loading.remove>Guardar</span>
                                <span wire:loading>Guardando...</span>
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Role Card -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium dark:text-white">{{ $role->name }}</h4>
                            <div class="flex items-center gap-1">
                                <button
                                    type="button"
                                    wire:click="startEdit({{ $role->id }})"
                                    class="p-1.5 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors"
                                    title="Editar rol"
                                >
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>
                                <button
                                    type="button"
                                    wire:click="deleteRole({{ $role->id }})"
                                    wire:confirm="¿Estás seguro de eliminar este rol?"
                                    class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                    title="Eliminar rol"
                                >
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Permission Badges -->
                        @php
                            $permissions = [
                                'can_manage_settings' => ['label' => 'Gestionar configuración', 'color' => 'purple'],
                                'can_manage_roles' => ['label' => 'Gestionar roles', 'color' => 'indigo'],
                                'can_add_edit_tasks' => ['label' => 'Agregar/editar tareas', 'color' => 'blue'],
                                'can_reorder_tasks' => ['label' => 'Reordenar tareas', 'color' => 'cyan'],
                                'can_upload_deliverables' => ['label' => 'Subir entregables', 'color' => 'teal'],
                                'can_approve_tasks' => ['label' => 'Aprobar tareas', 'color' => 'green'],
                                'can_edit_status' => ['label' => 'Editar estado', 'color' => 'orange'],
                                'can_view_audit' => ['label' => 'Ver auditoría', 'color' => 'gray'],
                            ];
                        @endphp
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($permissions as $field => $info)
                                @if($role->$field)
                                    <span class="px-2 py-0.5 text-xs rounded-full font-medium
                                        bg-{{ $info['color'] }}-100 text-{{ $info['color'] }}-800
                                        dark:bg-{{ $info['color'] }}-900/30 dark:text-{{ $info['color'] }}-400">
                                        {{ $info['label'] }}
                                    </span>
                                @endif
                            @endforeach
                        </div>

                        <!-- Member count -->
                        @php
                            $memberCount = $role->members()->count();
                        @endphp
                        @if($memberCount > 0)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                {{ $memberCount }} miembro(s) asignado(s)
                            </p>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <p class="text-gray-500 dark:text-gray-400 text-center py-6">
            No hay roles definidos. Crea un rol para empezar a gestionar permisos.
        </p>
    @endif
</div>
