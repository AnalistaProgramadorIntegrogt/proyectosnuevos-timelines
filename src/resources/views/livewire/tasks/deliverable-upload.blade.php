<div class="project-card p-6 space-y-6" x-data="{ dragOver: false }">
    <h3 class="text-lg font-semibold dark:text-white">Subir Entregable</h3>

    @php
        $target = $isSubtask ? $subtask : $task;
    @endphp

    @if($target->is_deliverable)
        <!-- Upload Form -->
        <form wire:submit="submitDeliverable" class="space-y-4">
            <!-- Drag & Drop / File Upload -->
            <div
                class="relative flex flex-col items-center justify-center w-full p-8 border-2 border-dashed rounded-lg cursor-pointer transition-colors duration-200"
                x-bind:class="dragOver ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700'"
                x-on:dragover.prevent="dragOver = true"
                x-on:dragleave.prevent="dragOver = false"
                x-on:drop.prevent="
                    dragOver = false;
                    $wire.upload('file', $event.dataTransfer.files[0]);
                "
            >
                <div class="flex flex-col items-center gap-2">
                    <!-- Upload icon -->
                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-semibold text-indigo-600 dark:text-indigo-400">Haz clic para seleccionar</span>
                        o arrastra un archivo aquí
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500">PDF, DOC, DOCX, XLS, XLSX, imágenes, ZIP — Máx. 100 MB</p>
                </div>

                <input
                    type="file"
                    wire:model="file"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                    x-on:change="dragOver = false"
                />
            </div>

            @error('file')
                <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
            @enderror

            @if($file)
                <div class="flex items-center gap-3 p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-indigo-700 dark:text-indigo-300 truncate">{{ $file->getClientOriginalName() }}</p>
                        <p class="text-xs text-indigo-500 dark:text-indigo-400">
                            {{ number_format($file->getSize() / 1024, 1) }} KB
                        </p>
                    </div>
                    <button type="button" wire:click="$set('file', null)" class="text-gray-400 hover:text-red-500 dark:hover:text-red-400">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Note -->
            <div>
                <label for="uploadNote" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nota (opcional)</label>
                <textarea
                    id="uploadNote"
                    wire:model="uploadNote"
                    rows="3"
                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Describe brevemente lo que contiene este entregable..."
                ></textarea>
                @error('uploadNote')
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
                <button type="submit" class="btn-primary inline-flex items-center gap-2" wire:loading.attr="disabled">
                    <svg wire:loading.remove class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    <span wire:loading.remove>Subir</span>
                    <span wire:loading>Subiendo...</span>
                </button>
            </div>
        </form>

        <!-- Previous Versions -->
        @if($versions->isNotEmpty())
            <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                <h4 class="text-md font-semibold dark:text-white mb-3">Versiones Anteriores</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Versión</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subido por</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Archivo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($versions as $version)
                                @php
                                    $submissionForVersion = $submissions->firstWhere('deliverable_version_id', $version->id);
                                    $approvalDecision = $submissionForVersion?->approvalDecision;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium dark:text-white">
                                        v{{ $version->version_number }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ $version->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                        {{ $version->uploader?->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <a href="{{ route('deliverables.download', $version) }}"
                                           class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 underline inline-flex items-center gap-1">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                            </svg>
                                            {{ $version->original_filename }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        @if($approvalDecision)
                                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                                {{ $approvalDecision->decision === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                                {{ $approvalDecision->decision === 'approved' ? 'Aprobado' : 'Rechazado' }}
                                            </span>
                                        @elseif($submissionForVersion)
                                            <span class="px-2 py-1 text-xs rounded-full font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                Pendiente
                                            </span>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($version->upload_note)
                                    <tr class="bg-gray-50 dark:bg-gray-800/50">
                                        <td colspan="5" class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400 italic">
                                            {{ $version->upload_note }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @else
        <p class="text-gray-500 dark:text-gray-400">Esta tarea no está configurada como entregable.</p>
    @endif
</div>
