<div class="project-card p-6 space-y-4 border-l-4 border-amber-400 dark:border-amber-500"
     x-data="{ showConfirm: false }"
     wire:loading.class="opacity-60 pointer-events-none">
    @if($hasDecision)
        <!-- === Decision already made: show result === -->
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0 mt-0.5">
                @if($group->gateDecision->outcome === 'viable')
                    <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                @else
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Decisión de Dependencia</h4>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($group->gateDecision->outcome === 'viable')
                            bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400
                        @else
                            bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400
                        @endif">
                        {{ $group->gateDecision->outcome === 'viable' ? '✅ Viable' : '❌ No Viable' }}
                    </span>
                </div>
                <p class="text-sm text-muted-500 dark:text-muted-400 mt-1">
                    Decidido por <span class="font-medium text-gray-700 dark:text-gray-300">{{ $group->gateDecision->decisionMaker?->name ?? '—' }}</span>
                    — {{ $group->gateDecision->created_at->format('d/m/Y H:i') }}
                </p>
                @if($group->gateDecision->notes)
                    <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-600">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Notas:</span>
                            {{ $group->gateDecision->notes }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @elseif($allTasksApproved)
        <!-- === No decision yet and all tasks approved: show gate decision form === -->
        <div>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Decisión de Viabilidad</h3>
                    <p class="text-sm text-muted-500 dark:text-muted-400">
                        Todas las tareas de este grupo han sido aprobadas. Por favor, evalúa la viabilidad del proyecto para continuar.
                    </p>
                </div>
            </div>

            <!-- Notes textarea -->
            <div class="mb-4">
                <label for="gateNotes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Notas <span class="text-muted-400">(opcional)</span>
                </label>
                <textarea
                    id="gateNotes"
                    wire:model="notes"
                    rows="3"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm"
                    placeholder="Agrega notas o comentarios sobre la decisión..."
                ></textarea>
                @error('notes')
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Decision buttons -->
            <div x-show="!showConfirm" class="flex items-center gap-3">
                <button
                    type="button"
                    x-on:click="showConfirm = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white text-sm font-medium rounded-lg transition-colors duration-150 shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    ✅ Viable
                </button>
                <button
                    type="button"
                    x-on:click="showConfirm = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors duration-150 shadow-sm"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    ❌ No Viable
                </button>
            </div>

            <!-- Confirmation step -->
            <div x-show="showConfirm" x-cloak class="space-y-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    ¿Estás seguro de la decisión?
                </p>
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        wire:click="makeDecision('viable')"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 text-white text-sm font-medium rounded-lg transition-colors duration-150 disabled:opacity-50"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span wire:loading.remove>✅ Sí, es Viable</span>
                        <span wire:loading>Procesando...</span>
                    </button>
                    <button
                        type="button"
                        wire:click="makeDecision('nonviable')"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors duration-150 disabled:opacity-50"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span wire:loading.remove>❌ No, No es Viable</span>
                        <span wire:loading>Procesando...</span>
                    </button>
                    <button
                        type="button"
                        x-on:click="showConfirm = false"
                        class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    @else
        <!-- === Tasks not yet all approved === -->
        <div class="flex items-center gap-3 py-2">
            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                <svg class="w-4 h-4 text-muted-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-sm text-muted-500 dark:text-muted-400">
                Pendiente de aprobación de todas las tareas para evaluar viabilidad.
            </p>
        </div>
    @endif
</div>
