<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-text-primary">
                    {{ __('Usuarios') }}
                </h1>
                <p class="text-sm text-text-muted mt-0.5">Gestión de usuarios y accesos del sistema</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="project-card">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-base font-bold text-text-primary">Todos los usuarios</h2>
                    <a href="{{ route('users.create') }}" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Usuario
                    </a>
                </div>

                @if(session('status'))
                    <div class="mb-4 font-semibold text-sm text-[var(--success)]">
                        {{ session('status') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 font-semibold text-sm text-[var(--integro-red)]">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-text-muted uppercase bg-surface-muted dark:bg-surface-elevated">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-semibold">Nombre</th>
                                <th scope="col" class="px-6 py-3 font-semibold">Email</th>
                                <th scope="col" class="px-6 py-3 font-semibold">Rol</th>
                                <th scope="col" class="px-6 py-3 font-semibold text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-border">
                            @foreach($users as $user)
                                <tr class="bg-surface dark:bg-surface hover:bg-surface-muted dark:hover:bg-surface-hover transition-colors">
                                    <td class="px-6 py-4 font-medium text-text-primary">
                                        {{ $user->name }}
                                        @if($user->azure_id)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                Microsoft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-text-secondary">{{ $user->email }}</td>
                                    <td class="px-6 py-4">
                                        @foreach($user->roles as $role)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-[var(--integro-blue)] text-white">
                                                {{ ucfirst($role->name) }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('users.edit', $user) }}" class="text-text-muted hover:text-text-primary transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            @if(auth()->id() !== $user->id)
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-text-muted hover:text-[var(--integro-red)] transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
