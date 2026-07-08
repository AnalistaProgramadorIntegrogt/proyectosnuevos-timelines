<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-bold text-text-primary">
                {{ __('Nuevo Proyecto') }}
            </h1>
            <p class="text-sm text-text-muted mt-0.5">Configura un nuevo proyecto de desarrollo</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="project-card">
                <form method="POST" action="{{ route('projects.store') }}" class="space-y-6">
                    @csrf

                    {{-- Template Selector --}}
                    <div class="border-b border-[var(--border-soft)] pb-6 mb-6">
                        <h3 class="text-base font-bold text-text-primary mb-4">
                            {{ __('Plantilla de Proceso') }}
                        </h3>

                        <div>
                            <x-label for="process_template_version_id" value="{{ __('Seleccionar plantilla de proceso (opcional)') }}" />
                            <select id="process_template_version_id" name="process_template_version_id" class="border-[var(--border-soft)] bg-surface text-text-primary focus:border-[var(--integro-red)] focus:ring-[var(--integro-red)] rounded block mt-1 w-full">
                                <option value="">— Sin plantilla —</option>
                                @foreach ($templates as $template)
                                    @php $ver = $template->latestPublishedVersion; @endphp
                                    @if ($ver)
                                        <option value="{{ $ver->id }}" {{ old('process_template_version_id') == $ver->id ? 'selected' : '' }}>
                                            {{ $template->name }} (v{{ $ver->version_number }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-text-muted">
                                {{ __('Si selecciona una plantilla, los grupos y tareas se precargarán automáticamente.') }}
                            </p>
                            <x-input-error for="process_template_version_id" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-label for="name" value="{{ __('Nombre del Proyecto') }}" />
                        <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        <x-input-error for="name" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="code" value="{{ __('Código (opcional)') }}" />
                        <x-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code')" />
                        <x-input-error for="code" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="description" value="{{ __('Descripción') }}" />
                        <textarea id="description" name="description" rows="4" class="border-[var(--border-soft)] bg-surface text-text-primary focus:border-[var(--integro-red)] focus:ring-[var(--integro-red)] rounded block mt-1 w-full">{{ old('description') }}</textarea>
                        <x-input-error for="description" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="start_date" value="{{ __('Fecha de Inicio') }}" />
                        <x-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', date('Y-m-d'))" required />
                        <x-input-error for="start_date" class="mt-2" />
                    </div>

                    <input type="hidden" name="default_approver_id" value="{{ auth()->id() }}">
                    <p class="text-sm text-text-muted">
                        Aprobador por defecto: <strong class="text-text-primary">{{ auth()->user()->name }}</strong>
                    </p>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-[var(--border-soft)]">
                        <a href="{{ route('projects.index') }}" class="btn-secondary text-xs">
                            Cancelar
                        </a>
                        <x-button>
                            {{ __('Crear Proyecto') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
