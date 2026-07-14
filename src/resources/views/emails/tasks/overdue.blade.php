<x-mail::message>
# Tarea Vencida

Estimado(a) **{{ $user->name }}**,

Se le notifica que una de sus tareas asignadas ha superado la fecha límite de entrega y se encuentra **ATRASADA**.

<x-mail::panel>
**Proyecto:** {{ $task->projectGroup->project->name ?? 'N/A' }}<br>
**Tarea:** {{ $task->title }}<br>
**Fecha Límite:** {{ $task->calculated_end_date ? $task->calculated_end_date->format('d/m/Y') : 'N/A' }}<br>
**Días de Retraso:** {{ $daysOverdue }} días
</x-mail::panel>

@if(isset($hasBoss) && $hasBoss)
> *Nota: Se ha enviado una copia de este aviso a su jefe directo.*
@endif

<x-mail::button :url="route('projects.roadmap', $task->projectGroup->project->id ?? 1)">
Ver en Plataforma
</x-mail::button>

<div style="text-align: center; border-top: 1px solid #e2e8f0; padding-top: 20px; margin-top: 30px;">
<span style="color: #64748b; font-size: 14px;">Atentamente,</span><br>
<strong style="color: #334155; font-size: 14px;">El equipo de {{ config('app.name') }}</strong>
</div>
</x-mail::message>
