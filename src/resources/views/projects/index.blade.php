<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-text-primary">
                    {{ __('Proyectos') }}
                </h1>
                <p class="text-sm text-text-muted mt-0.5">Gestiona los proyectos de desarrollo</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="project-card">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-base font-bold text-text-primary">Todos los proyectos</h2>
                    <a href="{{ route('projects.create') }}" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Proyecto
                    </a>
                </div>
                @livewire('projects.project-list')
            </div>
        </div>
    </div>
</x-app-layout>
