<div class="project-card p-6 space-y-6" x-data="{ inviteForm: false }">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold dark:text-white">Miembros del Proyecto</h3>
        <button
            type="button"
            x-on:click="inviteForm = !inviteForm"
            class="btn-primary inline-flex items-center gap-2 text-sm"
        >
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Agregar miembro
        </button>
    </div>

    <!-- Current Members List -->
    @if($members->isNotEmpty())
        <div class="space-y-3">
            @foreach($members as $member)
                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <div class="flex items-center gap-3">
                        <!-- Avatar -->
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                            @if($member->user?->profile_photo_url)
                                <img src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}"
                                     class="w-10 h-10 rounded-full object-cover">
                            @else
                                <span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ strtoupper(substr($member->user?->name ?? '?', 0, 2)) }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-medium dark:text-white">{{ $member->user?->name ?? 'Usuario eliminado' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $member->user?->email }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Role Badge & Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button
                                type="button"
                                x-on:click="open = !open"
                                class="px-2 py-1 text-xs rounded-full font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 hover:bg-indigo-200 dark:hover:bg-indigo-800/50 transition-colors"
                            >
                                {{ $member->role?->name ?? 'Sin rol' }}
                                <svg class="w-3 h-3 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div
                                x-show="open"
                                x-on:click.outside="open = false"
                                x-cloak
                                class="absolute right-0 mt-1 w-48 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg z-10 py-1"
                            >
                                @foreach($roles as $role)
                                    <button
                                        type="button"
                                        wire:click="changeRole({{ $member->id }}, {{ $role->id }})"
                                        x-on:click="open = false"
                                        class="block w-full text-left px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600 {{ $member->project_role_id === $role->id ? 'text-indigo-600 dark:text-indigo-400 font-medium' : 'text-gray-700 dark:text-gray-300' }}"
                                    >
                                        {{ $role->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Remove Button -->
                        <button
                            type="button"
                            wire:click="confirmRemoveMember({{ $member->id }})"
                            class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                            title="Eliminar miembro"
                        >
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 dark:text-gray-400 text-center py-6">No hay miembros en este proyecto además del propietario.</p>
    @endif

    <!-- Invite Form -->
    <div x-show="inviteForm" x-cloak class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 space-y-4">
        <h4 class="font-medium dark:text-white">Agregar usuario del sistema</h4>
        <p class="text-xs text-gray-500 dark:text-gray-400">Selecciona un usuario registrado en el sistema para agregarlo al proyecto.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- User Selector (searchable dropdown) -->
            <div>
                <label for="inviteUser" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Usuario</label>
                <select
                    id="inviteUser"
                    wire:model="email"
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">Seleccionar usuario...</option>
                    @foreach(\App\Models\User::where('id', '!=', $project->owner_id)->orderBy('name')->get() as $user)
                        @php
                            $isMember = $members->firstWhere('user_id', $user->id);
                        @endphp
                        @if(!$isMember)
                            <option value="{{ $user->email }}" {{ old('email') == $user->email ? 'selected' : '' }}>
                                {{ $user->name }} — {{ $user->email }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @error('email')
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div>
                <label for="inviteRole" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rol</label>
                <select
                    id="inviteRole"
                    wire:model="selectedRole"
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">Seleccionar rol...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('selectedRole')
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <button
                type="button"
                x-on:click="inviteForm = false"
                class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
            >
                Cancelar
            </button>
            <button
                type="button"
                wire:click="addMember"
                wire:loading.attr="disabled"
                class="btn-primary inline-flex items-center gap-2 text-sm disabled:opacity-50"
            >
                <span wire:loading.remove>Agregar al proyecto</span>
                <span wire:loading>Agregando...</span>
            </button>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
        x-data="{ show: @entangle('confirmingRemove') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md mx-4 w-full" x-on:click.outside="$wire.cancelRemove()">
            <h4 class="text-lg font-semibold dark:text-white mb-2">Confirmar eliminación</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                ¿Estás seguro de que deseas eliminar a este miembro del proyecto? Esta acción no se puede deshacer.
            </p>
            <div class="flex items-center justify-end gap-2">
                <button
                    type="button"
                    wire:click="cancelRemove"
                    class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                >
                    Cancelar
                </button>
                <button
                    type="button"
                    wire:click="removeMember({{ $confirmingRemove }})"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors"
                >
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</div>
