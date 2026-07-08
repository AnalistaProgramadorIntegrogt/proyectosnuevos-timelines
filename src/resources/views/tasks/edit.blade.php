<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Tarea') }}: {{ $task->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="project-card">
                <div class="mb-6">
                    <p class="text-sm text-muted-500 dark:text-muted-400">
                        Proyecto: <a href="{{ route('projects.show', $project) }}" class="text-primary-600 dark:text-primary-400 hover:underline">{{ $project->name }}</a>
                        @if($task->projectGroup)
                            / {{ $task->projectGroup->name }}
                        @endif
                    </p>
                </div>

                <form method="POST" action="{{ route('tasks.update', $task) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Título de la Tarea <span class="text-red-500">*</span>
                            </label>
                            <input id="title" type="text" name="title" value="{{ old('title', $task->title) }}" required
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <x-input-error for="title" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Descripción
                            </label>
                            <textarea id="description" name="description" rows="4"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $task->description) }}</textarea>
                            <x-input-error for="description" class="mt-2" />
                        </div>

                        <!-- Duration Days -->
                        <div>
                            <label for="duration_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Duración (días) <span class="text-red-500">*</span>
                            </label>
                            <input id="duration_days" type="number" name="duration_days" value="{{ old('duration_days', $task->duration_days) }}" min="1" max="30" required
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <p class="mt-1 text-xs text-muted-500 dark:text-muted-400">Cambiar este valor recalculará la línea de tiempo del proyecto.</p>
                            <x-input-error for="duration_days" class="mt-2" />
                        </div>

                        <!-- Status (read-only) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Estado
                            </label>
                            <div class="w-full rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 px-3 py-2 text-sm bg-gray-50 dark:bg-gray-800">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($task->status === 'en_proceso') status-en-proceso
                                    @elseif($task->status === 'entregado') status-entregado
                                    @elseif($task->status === 'aprobado') status-aprobado
                                    @elseif($task->status === 'atrasado') status-atrasado
                                    @elseif($task->status === 'rechazado') status-rechazado
                                    @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $task->status ?? 'pending')) }}
                                </span>
                                <span class="ml-2 text-xs text-muted-400 dark:text-muted-500">(El estado se actualiza automáticamente)</span>
                            </div>
                            <x-input-error for="status" class="mt-2" />
                        </div>

                        <!-- Responsible User -->
                        <div>
                            <label for="responsible_user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Responsable
                            </label>
                            <select id="responsible_user_id" name="responsible_user_id"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">— Sin asignar —</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('responsible_user_id', $task->responsible_user_id) == $user->id)>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="responsible_user_id" class="mt-2" />
                        </div>

                        <!-- Explicit Approver -->
                        <div>
                            <label for="explicit_approver_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Aprobador explícito
                            </label>
                            <select id="explicit_approver_id" name="explicit_approver_id"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">— Sin asignar —</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('explicit_approver_id', $task->explicit_approver_id) == $user->id)>{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="explicit_approver_id" class="mt-2" />
                        </div>
                    </div>

                    <!-- Read-only info -->
                    <div class="bg-surface-elevated dark:bg-surface-dark-elevated rounded-lg p-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-muted-500 dark:text-muted-400">Fecha inicio calculada:</span>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $task->calculated_start_date ? $task->calculated_start_date->format('d/m/Y') : '—' }}</p>
                        </div>
                        <div>
                            <span class="text-muted-500 dark:text-muted-400">Fecha fin calculada:</span>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $task->calculated_end_date ? $task->calculated_end_date->format('d/m/Y') : '—' }}</p>
                        </div>
                        <div>
                            <span class="text-muted-500 dark:text-muted-400">Tipo:</span>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $task->is_deliverable ? 'Entregable' : 'No entregable' }}</p>
                        </div>
                    </div>

                    <!-- Form actions -->
                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-muted-100 dark:border-muted-700">
                        <a href="{{ route('tasks.show', $task) }}" class="btn-secondary text-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="btn-primary text-sm">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
