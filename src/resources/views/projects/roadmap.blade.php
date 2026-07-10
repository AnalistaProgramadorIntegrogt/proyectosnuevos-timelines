<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $project->name }} - Roadmap
        </h2>
    </x-slot>

    <!-- Frappe Gantt CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.6.1/frappe-gantt.css">

    <style>
        /* Custom Overrides for Frappe Gantt to match our Tailwind theme */
        .gantt .grid-header { fill: #f8fafc; }
        .dark .gantt .grid-header { fill: #1e293b; }
        .gantt .grid-row { fill: #ffffff; }
        .dark .gantt .grid-row { fill: #0f172a; }
        .gantt .grid-row:nth-child(even) { fill: #f8fafc; }
        .dark .gantt .grid-row:nth-child(even) { fill: #1e293b; }
        .gantt .tick { stroke: #e2e8f0; }
        .dark .gantt .tick { stroke: #334155; }
        .gantt .upper-text, .gantt .lower-text { fill: #64748b; font-size: 12px; }
        .dark .gantt .upper-text, .dark .gantt .lower-text { fill: #94a3b8; }
        
        /* Bar text */
        .gantt .bar-label { fill: #fff; font-weight: 500; font-size: 12px; }
        
        /* Status Colors mapping */
        .gantt .bar-wrapper.status-en-proceso .bar { fill: #E9A15B; }
        .gantt .bar-wrapper.status-en-proceso .bar-progress { fill: #c78342; }
        
        .gantt .bar-wrapper.status-entregado .bar,
        .gantt .bar-wrapper.status-aprobado .bar,
        .gantt .bar-wrapper.status-completed-viable .bar,
        .gantt .bar-wrapper.status-completed .bar { fill: #5C9B68; }
        
        .gantt .bar-wrapper.status-entregado .bar-progress,
        .gantt .bar-wrapper.status-aprobado .bar-progress { fill: #4a7d53; }
        
        .gantt .bar-wrapper.status-atrasado .bar,
        .gantt .bar-wrapper.status-completed-nonviable .bar { fill: #D56E6E; }
        .gantt .bar-wrapper.status-atrasado .bar-progress { fill: #b35959; }
        
        .gantt .bar-wrapper.status-rechazado .bar { fill: #A7A7AE; }
        .gantt .bar-wrapper.status-rechazado .bar-progress { fill: #86868d; }
        
        .gantt .bar-wrapper.status-pending .bar,
        .gantt .bar-wrapper.status-locked .bar { fill: #CBD5E1; }
        .gantt .bar-wrapper.status-pending .bar-progress { fill: #a5b4c4; }
        .gantt .bar-wrapper.status-pending .bar-label { fill: #475569; }
        
        /* Arrow path */
        .gantt .arrow { stroke: #94a3b8; stroke-width: 1.5; }
        
        /* Popup */
        .gantt-container .popup-wrapper {
            background: #fff;
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .dark .gantt-container .popup-wrapper {
            background: #1e293b;
            border-color: #334155;
            color: #f8fafc;
        }
        .gantt-container .popup-wrapper .title { font-weight: 600; font-size: 14px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 8px;}
        .dark .gantt-container .popup-wrapper .title { border-color: #334155; }
        .gantt-container .popup-wrapper .subtitle { font-size: 12px; color: #64748b; }
        .dark .gantt-container .popup-wrapper .subtitle { color: #94a3b8; }
        .gantt-container .popup-wrapper .pointer { display: none; }
        
        .gantt-target { background: white; border-radius: 0.5rem; }
        .dark .gantt-target { background: #1e293b; }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Project Header Card -->
            <div class="project-card mb-8">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 flex-wrap mb-2">
                            <a href="{{ route('projects.index') }}" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" title="Volver a proyectos">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            </a>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $project->name }}</h1>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('projects.show', $project) }}" class="btn-secondary text-xs !px-3 !py-1 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            Ver Lista Vertical
                        </a>
                    </div>
                </div>
                
                <!-- Zoom Controls -->
                <div class="mt-4 flex items-center gap-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Zoom:</span>
                    <button type="button" class="btn-zoom text-xs px-2 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-white" data-scale="Day">Día</button>
                    <button type="button" class="btn-zoom text-xs px-2 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-white" data-scale="Week">Semana</button>
                    <button type="button" class="btn-zoom text-xs px-2 py-1 bg-gray-200 border border-gray-300 rounded-md font-bold dark:bg-gray-700 dark:border-gray-600 dark:text-white" data-scale="Month">Mes</button>
                </div>
            </div>

            <!-- Gantt Container -->
            @if(count($ganttTasks) > 0)
                <div class="shadow rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-slate-800">
                    <div class="w-full overflow-auto max-h-[calc(100vh-220px)] custom-scrollbar">
                        <div class="gantt-target" id="gantt"></div>
                    </div>
                </div>
            @else
                <div class="project-card text-center py-12">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No hay tareas programadas</h3>
                    <p class="text-sm text-muted-500 dark:text-muted-400">
                        Aún no se han generado las fechas de las tareas para mostrar el Roadmap.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- Frappe Gantt JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/frappe-gantt/0.6.1/frappe-gantt.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tasks = @json($ganttTasks);
            
            if (tasks.length === 0) return;

            var gantt = new Gantt("#gantt", tasks, {
                header_height: 50,
                column_width: 30,
                step: 24,
                view_modes: ['Quarter Day', 'Half Day', 'Day', 'Week', 'Month'],
                bar_height: 25,
                bar_corner_radius: 4,
                arrow_curve: 5,
                padding: 18,
                view_mode: 'Month',
                date_format: 'YYYY-MM-DD',
                custom_popup_html: function(task) {
                    var end_date = new Date(task.end);
                    end_date.setDate(end_date.getDate() - 1); // Substract 1 day because frappe makes end date exclusive
                    var dateStr = task.start + ' a ' + end_date.toISOString().split('T')[0];
                    return `
                        <div class="popup-wrapper">
                            <div class="title">${task.name}</div>
                            <div class="subtitle">Fechas: ${dateStr}</div>
                            <div class="subtitle mt-1">Progreso: ${task.progress}%</div>
                            <div class="text-xs text-indigo-500 mt-2">Doble clic para ver detalles</div>
                        </div>
                    `;
                },
                on_click: function (task) {
                    // Navigate to task on single or double click depending on preference
                },
                on_date_change: function(task, start, end) {
                    // Disabled by default (read only)
                },
                on_progress_change: function(task, progress) {
                    // Disabled by default (read only)
                }
            });

            // Prevent drag and drop since we want it read-only
            document.querySelectorAll('.bar-wrapper').forEach(function(el) {
                el.style.pointerEvents = 'auto'; // allow click
            });
            document.querySelectorAll('.handle-group').forEach(function(el) {
                el.style.display = 'none'; // hide drag handles
            });

            // Handle double click for navigation
            document.getElementById('gantt').addEventListener('dblclick', function(e) {
                var bar = e.target.closest('.bar-wrapper');
                if (bar) {
                    var taskId = bar.getAttribute('data-id');
                    var realId = taskId.replace('task_', '');
                    window.location.href = '/tasks/' + realId;
                }
            });

            // Handle zoom controls
            document.querySelectorAll('.btn-zoom').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    var scale = e.target.getAttribute('data-scale');
                    gantt.change_view_mode(scale);
                    
                    // Update button active state
                    document.querySelectorAll('.btn-zoom').forEach(b => {
                        b.classList.remove('bg-gray-200', 'font-bold', 'dark:bg-gray-700');
                        b.classList.add('bg-white', 'dark:bg-gray-800');
                    });
                    e.target.classList.add('bg-gray-200', 'font-bold', 'dark:bg-gray-700');
                    e.target.classList.remove('bg-white', 'dark:bg-gray-800');
                });
            });
        });
    </script>
</x-app-layout>
