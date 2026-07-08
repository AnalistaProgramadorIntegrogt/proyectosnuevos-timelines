<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Proyecto') }}: {{ $project->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-label for="name" value="{{ __('Nombre del Proyecto') }}" />
                        <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $project->name)" required />
                        <x-input-error for="name" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="code" value="{{ __('Código (opcional)') }}" />
                        <x-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code', $project->code)" />
                        <x-input-error for="code" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="description" value="{{ __('Descripción') }}" />
                        <textarea id="description" name="description" rows="4" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">{{ old('description', $project->description) }}</textarea>
                        <x-input-error for="description" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="start_date" value="{{ __('Fecha de Inicio') }}" />
                        <x-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', $project->start_date?->format('Y-m-d'))" />
                        <x-input-error for="start_date" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="lifecycle_status" value="{{ __('Estado del Ciclo de Vida') }}" />
                        <select id="lifecycle_status" name="lifecycle_status" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                            <option value="en_proceso" @selected(old('lifecycle_status', $project->lifecycle_status) === 'en_proceso')>En Proceso</option>
                            <option value="entregado" @selected(old('lifecycle_status', $project->lifecycle_status) === 'entregado')>Entregado</option>
                            <option value="aprobado" @selected(old('lifecycle_status', $project->lifecycle_status) === 'aprobado')>Aprobado</option>
                            <option value="atrasado" @selected(old('lifecycle_status', $project->lifecycle_status) === 'atrasado')>Atrasado</option>
                            <option value="rechazado" @selected(old('lifecycle_status', $project->lifecycle_status) === 'rechazado')>Rechazado</option>
                        </select>
                        <x-input-error for="lifecycle_status" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Cancelar
                        </a>
                        <x-button>
                            {{ __('Guardar Cambios') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
