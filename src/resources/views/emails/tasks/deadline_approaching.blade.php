<x-mail::message>
# Fecha de Entrega Próxima

Estimado(a) **{{ $user->name }}**,

Se le notifica que una de sus tareas asignadas está próxima a su fecha límite de entrega (faltan {{ $daysLeft }} días).

<x-mail::panel>
**Proyecto:** {{ $task->projectGroup->project->name ?? 'N/A' }}<br>
**Tarea:** {{ $task->title }}<br>
**Fecha Límite:** {{ $task->calculated_end_date ? $task->calculated_end_date->format('d/m/Y') : 'N/A' }}
</x-mail::panel>

<x-mail::button :url="route('projects.roadmap', $task->projectGroup->project->id ?? 1)">
Ver en Plataforma
</x-mail::button>

<div style="text-align: center; border-top: 1px solid #e2e8f0; padding-top: 20px; margin-top: 30px;">
<span style="color: #64748b; font-size: 14px;">Atentamente,</span><br>
<strong style="color: #334155; font-size: 14px;">El equipo de {{ config('app.name') }}</strong>
</div>
</x-mail::message>
