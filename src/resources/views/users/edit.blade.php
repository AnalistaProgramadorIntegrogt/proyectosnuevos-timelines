<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('users.index') }}" class="text-text-muted hover:text-text-primary transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-text-primary">
                    {{ __('Editar Usuario') }}
                </h1>
                <p class="text-sm text-text-muted mt-0.5">{{ $user->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="project-card">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <x-label for="name" value="{{ __('Nombre Completo') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                            <x-input-error for="name" class="mt-2" />
                        </div>

                        <div>
                            <x-label for="email" value="{{ __('Correo Electrónico') }}" />
                            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                            <x-input-error for="email" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-label for="password" value="{{ __('Nueva Contraseña (Opcional)') }}" />
                                <x-input id="password" class="block mt-1 w-full" type="password" name="password" />
                                <x-input-error for="password" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="password_confirmation" value="{{ __('Confirmar Nueva Contraseña') }}" />
                                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" />
                            </div>
                        </div>

                        <div>
                            <x-label for="role" value="{{ __('Rol del Sistema') }}" />
                            @php
                                $currentRole = old('role') ?? $user->roles->first()?->name;
                            @endphp
                            <div x-data="{
                                open: false,
                                value: '{{ $currentRole }}',
                                text: '{{ $currentRole ? ucfirst($currentRole) : 'Selecciona un rol' }}',
                                select(val, txt) {
                                    this.value = val;
                                    this.text = txt;
                                    this.open = false;
                                }
                            }" class="relative mt-1">
                                <button type="button" @click="open = !open" 
                                    class="relative w-full border border-surface-border bg-surface dark:bg-surface-elevated text-left text-text-primary rounded-md shadow-sm pl-3 pr-10 py-2 focus:outline-none focus:ring-1 focus:ring-[var(--integro-red)] focus:border-[var(--integro-red)] sm:text-sm transition-colors">
                                    <span class="block truncate" x-text="text"></span>
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="h-5 w-5 text-text-muted" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </button>

                                <div x-show="open" @click.away="open = false" x-transition
                                    class="absolute z-10 mt-1 w-full bg-surface dark:bg-surface-elevated shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm" style="display: none;">
                                    @foreach($roles as $role)
                                        <div @click="select('{{ $role->name }}', '{{ ucfirst($role->name) }}')"
                                            class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-[var(--integro-red)] hover:text-white transition-colors"
                                            :class="{'bg-[var(--integro-red)] text-white': value === '{{ $role->name }}', 'text-text-primary': value !== '{{ $role->name }}'}">
                                            <span class="block truncate font-normal" :class="{'font-semibold': value === '{{ $role->name }}'}">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                            <span x-show="value === '{{ $role->name }}'" class="absolute inset-y-0 right-0 flex items-center pr-4" :class="{'text-white': value === '{{ $role->name }}', 'text-[var(--integro-red)]': value !== '{{ $role->name }}'}">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="role" :value="value">
                            </div>
                            <x-input-error for="role" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <a href="{{ route('users.index') }}" class="btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn-primary">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
