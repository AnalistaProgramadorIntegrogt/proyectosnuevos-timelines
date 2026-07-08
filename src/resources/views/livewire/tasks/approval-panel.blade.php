<div class="project-card p-6 space-y-6" x-data="{ showRejectForm: false }">
    <h3 class="text-lg font-semibold dark:text-white">Panel de Aprobación</h3>

    @if($latestSubmission)
        <!-- Latest Submission Info -->
        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-3">
            <h4 class="font-medium dark:text-white">Último Entregable</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Subido por:</span>
                    <span class="ml-1 font-medium dark:text-white">{{ $latestSubmission->submitter?->name ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Fecha:</span>
                    <span class="ml-1 font-medium dark:text-white">{{ $latestSubmission->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($latestSubmission->deliverableVersion)
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Versión:</span>
                        <span class="ml-1 font-medium dark:text-white">v{{ $latestSubmission->deliverableVersion->version_number }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Archivo:</span>
                        <a href="{{ route('deliverables.download', $latestSubmission->deliverableVersion) }}"
                           class="ml-1 text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 underline">
                            {{ $latestSubmission->deliverableVersion->original_filename }}
                        </a>
                    </div>
                @endif
            </div>
            @if($latestSubmission->notes)
                <div class="mt-2 p-3 bg-white dark:bg-gray-700 rounded border dark:border-gray-600">
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $latestSubmission->notes }}</p>
                </div>
            @endif

            <!-- Approval Status -->
            @if($latestSubmission->approvalDecision)
                <div class="flex items-center gap-2 mt-2">
                    @if($latestSubmission->approvalDecision->decision === 'approved')
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Aprobado
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Rechazado
                        </span>
                    @endif
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        por {{ $latestSubmission->approvalDecision->approver?->name ?? '—' }}
                        — {{ $latestSubmission->approvalDecision->created_at->format('d/m/Y H:i') }}
                    </span>
                </div>
                @if($latestSubmission->approvalDecision->note)
                    <div class="mt-2 p-3 bg-white dark:bg-gray-700 rounded border dark:border-gray-600">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium dark:text-white">Razón:</span> {{ $latestSubmission->approvalDecision->note }}
                        </p>
                    </div>
                @endif
            @else
                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pendiente de revisión
                    </span>
                </div>
            @endif
        </div>

        <!-- Approve / Reject Actions -->
        @if($canApprove && !$latestSubmission->approvalDecision)
            <div class="flex items-center gap-3 pt-2">
                <button
                    type="button"
                    wire:click="approve"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white text-sm font-medium rounded-lg transition-colors duration-150 disabled:opacity-50"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span wire:loading.remove>Aprobar</span>
                    <span wire:loading>Procesando...</span>
                </button>
                <button
                    type="button"
                    x-on:click="showRejectForm = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors duration-150"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Rechazar
                </button>
            </div>

            <!-- Reject Reason Form -->
            <div x-show="showRejectForm" x-cloak class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 space-y-3">
                <label for="rejectReason" class="block text-sm font-medium text-red-800 dark:text-red-300">
                    Razón del rechazo
                </label>
                <textarea
                    id="rejectReason"
                    wire:model="reason"
                    rows="3"
                    class="w-full rounded-md border-red-300 dark:border-red-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                    placeholder="Explica por qué se rechaza este entregable..."
                ></textarea>
                @error('reason')
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        wire:click="reject"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors duration-150 disabled:opacity-50"
                    >
                        <span wire:loading.remove>Confirmar Rechazo</span>
                        <span wire:loading>Procesando...</span>
                    </button>
                    <button
                        type="button"
                        x-on:click="showRejectForm = false; $wire.set('reason', '')"
                        class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        @endif

        <!-- Approval History -->
        @if($approvalHistory->isNotEmpty())
            <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                <h4 class="text-md font-semibold dark:text-white mb-3">Historial de Aprobaciones</h4>
                <div class="space-y-3">
                    @foreach($approvalHistory as $submission)
                        @if($submission->approvalDecision)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex-shrink-0 mt-0.5">
                                    @if($submission->approvalDecision->decision === 'approved')
                                        <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium dark:text-white">
                                        {{ $submission->approvalDecision->decision === 'approved' ? 'Aprobado' : 'Rechazado' }}
                                        <span class="font-normal text-gray-500 dark:text-gray-400">
                                            por {{ $submission->approvalDecision->approver?->name ?? '—' }}
                                        </span>
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $submission->created_at->format('d/m/Y H:i') }}
                                        @if($submission->deliverableVersion)
                                            · v{{ $submission->deliverableVersion->version_number }}
                                            · <a href="{{ route('deliverables.download', $submission->deliverableVersion) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                                {{ $submission->deliverableVersion->original_filename }}
                                              </a>
                                        @endif
                                    </p>
                                    @if($submission->approvalDecision->note)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 italic">
                                            "{{ $submission->approvalDecision->note }}"
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <div class="text-center py-8">
            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-gray-500 dark:text-gray-400">No hay entregables pendientes de revisión.</p>
        </div>
    @endif
</div>
