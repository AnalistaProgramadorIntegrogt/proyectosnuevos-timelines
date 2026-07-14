<?php

namespace App\Http\Controllers;

use App\Models\AuditEvent;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display the specified task with full details.
     */
    public function show(Task $task)
    {
        $task->load([
            'projectGroup.project',
            'subtasks.responsible',
            'deliverableVersions.uploader',
            'submissions.deliverableVersion',
            'submissions.approvalDecision.approver',
            'submissions.submitter',
            'responsibles',
            'approvers',
            'auditEvents.user',
        ]);

        $project = $task->projectGroup->project;

        // Determine if current user can submit this task
        $canSubmit = $task->status === 'en_proceso'
            && Auth::id() === $task->responsible_user_id;

        // Determine if current user can edit
        $canEdit = Auth::user()->can('update', $project);

        // Determine if current user can reopen
        $canReopen = in_array($task->status, ['entregado', 'aprobado', 'rechazado'])
            && Auth::user()->can('update', $project);

        // Determine effective approvers (task-specific override or project default)
        $effectiveApprovers = collect();
        if ($task->approvers->isNotEmpty()) {
            $effectiveApprovers = $task->approvers;
        } elseif ($project->default_approver_id) {
            $default = \App\Models\User::find($project->default_approver_id);
            if ($default) $effectiveApprovers->push($default);
        }

        return view('tasks.show', compact('task', 'project', 'canSubmit', 'canEdit', 'canReopen', 'effectiveApprovers'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $task->load([
            'projectGroup.project',
            'responsibles',
            'approvers',
        ]);

        $project = $task->projectGroup->project;

        $this->authorize('update', $project);

        // Gather available users — project members with a fallback to all users
        $memberIds = $project->members()->pluck('user_id');
        
        $users = User::query()
            ->whereIn('id', $memberIds)
            ->orWhereIn('id', $task->responsibles->pluck('id'))
            ->orWhereIn('id', $task->approvers->pluck('id'))
            ->orderBy('name')
            ->get();

        // If no project members exist, show all users
        if ($users->isEmpty()) {
            $users = User::orderBy('name')->get();
        }

        return view('tasks.edit', compact('task', 'project', 'users'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        $project = $task->projectGroup->project;

        $this->authorize('update', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_days' => 'required|integer|min:1|max:30',
            'responsible_users' => 'nullable|array',
            'responsible_users.*' => 'exists:users,id',
            'approver_users' => 'nullable|array',
            'approver_users.*' => 'exists:users,id',
            // Status is not directly editable from the form; auto-managed by the system
        ]);

        // Administrators/owners can set status to aprobado or rechazado via dedicated actions
        if ($request->has('status') && in_array($request->status, ['aprobado', 'rechazado']) && Auth::user()->can('update', $project)) {
            $validated['status'] = $request->status;
        }

        $beforeData = $task->toArray();

        $task->fill($request->except(['responsible_users', 'approver_users']));
        $needsRecalculation = $task->isDirty('duration_days') || $task->isDirty('real_end_date');
        $task->save();
        
        if ($request->has('responsible_users')) {
            $task->responsibles()->sync($request->input('responsible_users', []));
        }

        if ($request->has('approver_users')) {
            $task->approvers()->sync($request->input('approver_users', []));
        }

        // Recalculate timeline for the project if duration or real end date changes
        if ($needsRecalculation) {
            $this->recalculateTimeline($project);
            Artisan::call('tasks:update-statuses');
        }

        // Audit event
        AuditEvent::create([
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'task_id' => $task->id,
            'action' => 'task_updated',
            'entity_type' => 'task',
            'entity_id' => (string) $task->id,
            'before_data' => $beforeData,
            'after_data' => $validated,
            'reason' => 'Actualización de tarea: ' . $task->title,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('flash.banner', 'Tarea actualizada exitosamente.');
    }

    /**
     * Submit the task for approval — change status to 'entregado'.
     */
    public function submit(Task $task)
    {
        $project = $task->projectGroup->project;

        // Only one of the responsibles or an authorized user can submit
        $isResponsible = $task->responsibles->contains('id', Auth::id());
        if (!$isResponsible && !Auth::user()->can('update', $project)) {
            abort(403, 'No tienes permiso para entregar esta tarea.');
        }

        if ($task->status !== 'en_proceso') {
            return redirect()->route('tasks.show', $task)
                ->with('flash.banner', 'Solo se pueden entregar tareas en estado "En Proceso".');
        }

        $beforeStatus = $task->status;
        $task->update(['status' => 'entregado']);

        // Audit event
        AuditEvent::create([
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'task_id' => $task->id,
            'action' => 'task_submitted',
            'entity_type' => 'task',
            'entity_id' => (string) $task->id,
            'before_data' => ['status' => $beforeStatus],
            'after_data' => ['status' => 'entregado'],
            'reason' => 'Tarea entregada para aprobación: ' . $task->title,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('flash.banner', 'Tarea entregada para aprobación.');
    }

    /**
     * Reopen a submitted/approved/rejected task — change status back to 'en_proceso'.
     */
    public function reopen(Request $request, Task $task)
    {
        $project = $task->projectGroup->project;

        $this->authorize('update', $project);

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if (!in_array($task->status, ['entregado', 'aprobado', 'rechazado'])) {
            return redirect()->route('tasks.show', $task)
                ->with('flash.banner', 'Solo se pueden reabrir tareas entregadas, aprobadas o rechazadas.');
        }

        $beforeStatus = $task->status;
        $task->update(['status' => 'en_proceso']);

        // Audit event
        AuditEvent::create([
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'task_id' => $task->id,
            'action' => 'task_reopened',
            'entity_type' => 'task',
            'entity_id' => (string) $task->id,
            'before_data' => ['status' => $beforeStatus],
            'after_data' => ['status' => 'en_proceso', 'reason' => $validated['reason']],
            'reason' => $validated['reason'],
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('flash.banner', 'Tarea reabierta exitosamente.');
    }

    /**
     * Recalculate timeline dates for the entire project based on start_date and task durations.
     */
    protected function recalculateTimeline(Project $project)
    {
        $project->load('groups.tasks');

        $currentDate = Carbon::parse($project->start_date);

        foreach ($project->groups->sortBy('order') as $group) {
            foreach ($group->tasks->sortBy('order') as $t) {
                $t->calculated_start_date = $currentDate->copy();
                $t->calculated_end_date = $currentDate->copy()->addDays($t->duration_days - 1);
                
                if (is_null($t->baseline_end_date)) {
                    $t->baseline_end_date = $t->calculated_end_date->copy();
                }

                $t->save();
                
                if ($t->real_end_date && Carbon::parse($t->real_end_date)->isAfter($t->calculated_end_date)) {
                    $currentDate = Carbon::parse($t->real_end_date)->copy()->addDay();
                } else {
                    $currentDate = $t->calculated_end_date->copy()->addDay();
                }
            }
        }
    }
}
