<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Repositorio Público') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Toolbar & Breadcrumbs -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <button wire:click="navigateTo(null)" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                                Inicio
                            </button>
                        </li>
                        @foreach($breadcrumbs as $crumb)
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                    <button wire:click="navigateTo({{ $crumb->id }})" class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">{{ $crumb->name }}</button>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </nav>

                @if($canManage)
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Nuevo
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <button wire:click="$set('showCreateFolderModal', true)" class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none transition">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                                Carpeta
                            </button>
                            <button wire:click="openUploadModal" class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none transition">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Subir Archivo
                            </button>
                        </x-slot>
                    </x-dropdown>
                @endif
            </div>

            <!-- Content Grid -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                @if($folders->isEmpty() && $documents->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path></svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Carpeta Vacía</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No hay documentos ni carpetas aquí.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        
                        <!-- Folders -->
                        @foreach($folders as $folder)
                            <div class="group relative flex flex-col p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-indigo-500 hover:shadow-sm transition-all bg-gray-50 dark:bg-gray-700/50">
                                <div class="flex items-start justify-between">
                                    <button wire:click="navigateTo({{ $folder->id }})" class="flex items-center gap-3 text-left w-full">
                                        <svg class="w-8 h-8 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                        <div>
                                            <h4 class="font-medium text-sm text-gray-900 dark:text-white line-clamp-1">{{ $folder->name }}</h4>
                                            @if($folder->description)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1 mt-0.5">{{ $folder->description }}</p>
                                            @endif
                                        </div>
                                    </button>
                                    @if($canManage)
                                        <x-dropdown align="right" width="48">
                                            <x-slot name="trigger">
                                                <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
                                                </button>
                                            </x-slot>
                                            <x-slot name="content">
                                                <button wire:click="deleteFolder({{ $folder->id }})" wire:confirm="¿Seguro que deseas eliminar esta carpeta y todo su contenido?" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30">
                                                    Eliminar
                                                </button>
                                            </x-slot>
                                        </x-dropdown>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <!-- Documents -->
                        @foreach($documents as $doc)
                            <div class="group relative flex flex-col p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:border-indigo-500 hover:shadow-sm transition-all bg-white dark:bg-gray-800">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-8 h-8 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        <div>
                                            <h4 class="font-medium text-sm text-gray-900 dark:text-white line-clamp-1" title="{{ $doc->name }}">{{ $doc->name }}</h4>
                                            @if($doc->latestVersion)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                    v{{ $doc->latestVersion->version_number }} • {{ number_format($doc->latestVersion->size / 1024, 1) }} KB
                                                </p>
                                            @else
                                                <p class="text-xs text-red-500 mt-0.5">Sin archivo</p>
                                            @endif
                                        </div>
                                    </div>
                                    <x-dropdown align="right" width="48">
                                        <x-slot name="trigger">
                                            <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
                                            </button>
                                        </x-slot>
                                        <x-slot name="content">
                                            @if($doc->latestVersion)
                                                <button wire:click="downloadVersion({{ $doc->latestVersion->id }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    Descargar Última
                                                </button>
                                            @endif
                                            <button wire:click="viewVersions({{ $doc->id }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                Historial de Versiones
                                            </button>
                                            @if($canManage)
                                                <button wire:click="openNewVersionModal({{ $doc->id }})" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    Subir Nueva Versión
                                                </button>
                                                <div class="border-t border-gray-200 dark:border-gray-600"></div>
                                                <button wire:click="deleteDocument({{ $doc->id }})" wire:confirm="¿Seguro que deseas eliminar el documento completo?" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30">
                                                    Eliminar
                                                </button>
                                            @endif
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Create Folder Modal -->
    <x-dialog-modal wire:model.live="showCreateFolderModal">
        <x-slot name="title">
            Nueva Carpeta
        </x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label for="newFolderName" value="Nombre de la carpeta" />
                    <x-input id="newFolderName" type="text" class="mt-1 block w-full" wire:model="newFolderName" placeholder="Ej: Contratos 2026" />
                    <x-input-error for="newFolderName" class="mt-2" />
                </div>
                <div>
                    <x-label for="newFolderDescription" value="Descripción (opcional)" />
                    <textarea id="newFolderDescription" wire:model="newFolderDescription" rows="2" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                    <x-input-error for="newFolderDescription" class="mt-2" />
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showCreateFolderModal', false)">Cancelar</x-secondary-button>
            <x-button class="ms-2" wire:click="createFolder" wire:loading.attr="disabled">Crear Carpeta</x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- Upload File Modal -->
    <x-dialog-modal wire:model.live="showUploadFileModal">
        <x-slot name="title">
            {{ $targetDocumentId ? 'Subir Nueva Versión' : 'Subir Documento' }}
        </x-slot>
        <x-slot name="content">
            <form wire:submit="uploadDocument" class="space-y-4">
                @if(!$targetDocumentId)
                    <div>
                        <x-label for="uploadFileName" value="Nombre del documento" />
                        <x-input id="uploadFileName" type="text" class="mt-1 block w-full" wire:model="uploadFileName" placeholder="Ej: Acta de Constitución" />
                        <x-input-error for="uploadFileName" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="uploadFileDescription" value="Descripción (opcional)" />
                        <textarea id="uploadFileDescription" wire:model="uploadFileDescription" rows="2" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                    </div>
                @else
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Estás subiendo una nueva versión para: <strong>{{ $uploadFileName }}</strong></p>
                        <x-label for="newVersionNote" value="Nota de la versión (opcional)" />
                        <x-input id="newVersionNote" type="text" class="mt-1 block w-full" wire:model="newVersionNote" placeholder="Ej: Se agregaron las firmas del comité" />
                    </div>
                @endif
                
                <div>
                    <x-label for="uploadFile" value="Archivo" />
                    <input type="file" id="uploadFile" wire:model="uploadFile" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-400" />
                    <div wire:loading wire:target="uploadFile" class="text-sm text-indigo-500 mt-2">Cargando archivo...</div>
                    <x-input-error for="uploadFile" class="mt-2" />
                </div>

                <button type="submit" class="hidden" id="submitUploadBtn"></button>
            </form>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showUploadFileModal', false)">Cancelar</x-secondary-button>
            <x-button class="ms-2" onclick="document.getElementById('submitUploadBtn').click()" wire:loading.attr="disabled">Subir</x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- Versions Modal -->
    <x-dialog-modal wire:model.live="showVersionsModal" maxWidth="2xl">
        <x-slot name="title">
            Historial de Versiones: {{ $viewingDocument ? $viewingDocument->name : '' }}
        </x-slot>
        <x-slot name="content">
            @if($viewingDocument)
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Versión</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Fecha</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Por</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Nota</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                            @foreach($viewingDocument->versions as $version)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white">v{{ $version->version_number }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $version->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $version->uploader?->name ?? 'N/A' }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $version->upload_note ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <button wire:click="downloadVersion({{ $version->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Descargar</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showVersionsModal', false)">Cerrar</x-secondary-button>
        </x-slot>
    </x-dialog-modal>

</div>
