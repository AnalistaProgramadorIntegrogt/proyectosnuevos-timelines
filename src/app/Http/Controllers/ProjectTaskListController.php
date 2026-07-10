<?php

namespace App\Http\Controllers;

use App\Models\AuditEvent;
use App\Models\Project;
use App\Models\ProjectGroup;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class ProjectTaskListController extends Controller
{
    /**
     * Display the task list editor for a project.
     */
    public function index(Project $project)
    {
        $this->authorize('update', $project);

        $project->load([
            'groups' => function ($q) {
                $q->orderBy('order');
            },
            'groups.tasks' => function ($q) {
                $q->orderBy('order');
            },
            'groups.tasks.responsible',
        ]);

        // Gather project members for the responsible dropdown
        $users = User::query()
            ->whereHas('projects', function ($q) use ($project) {
                $q->where('project_id', $project->id);
            })
            ->orWhere('id', $project->owner_id)
            ->orderBy('name')
            ->get();

        if ($users->isEmpty()) {
            $users = User::orderBy('name')->get();
        }

        return view('projects.task-list', compact('project', 'users'));
    }

    /**
     * Update a single task in-place.
     */
    public function update(Request $request, Task $task)
    {
        $project = $task->projectGroup->project;

        $this->authorize('update', $project);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'responsible_user_id' => 'nullable|exists:users,id',
            'duration_days' => 'sometimes|required|integer|min:1|max:30',
            'real_end_date' => 'nullable|date',
            'is_deliverable' => 'sometimes|boolean',
        ]);

        $beforeData = $task->only(array_keys($validated));

        $task->fill($validated);
        $needsRecalculation = $task->isDirty('duration_days') || $task->isDirty('real_end_date');
        $task->save();

        // Recalculate timeline if duration or real_end_date changed
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
            'reason' => 'Actualización desde editor de lista: ' . $task->title,
            'ip_address' => $request->ip(),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'task' => $task->fresh()->load('responsible'),
            ]);
        }

        return redirect()->route('projects.tasks.edit-list', $project)
            ->with('flash.banner', 'Tarea actualizada exitosamente.');
    }

    /**
     * Reorder tasks for a project.
     */
    public function reorder(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:tasks,id',
            'tasks.*.order' => 'required|integer|min:0',
        ]);

        $changes = [];

        foreach ($validated['tasks'] as $item) {
            $task = Task::findOrFail($item['id']);

            // Ensure the task belongs to this project
            if ($task->projectGroup->project_id !== $project->id) {
                continue;
            }

            $oldOrder = $task->order;
            if ((int) $item['order'] !== $oldOrder) {
                $changes[] = [
                    'task_id' => $task->id,
                    'title' => $task->title,
                    'old_order' => $oldOrder,
                    'new_order' => (int) $item['order'],
                ];
            }

            $task->update(['order' => (int) $item['order']]);
        }

        // Recalculate timeline after reorder
        $this->recalculateTimeline($project);
        Artisan::call('tasks:update-statuses');

        // Log audit event per changed task
        foreach ($changes as $change) {
            AuditEvent::create([
                'user_id' => Auth::id(),
                'project_id' => $project->id,
                'task_id' => $change['task_id'],
                'action' => 'task_reordered',
                'entity_type' => 'task',
                'entity_id' => (string) $change['task_id'],
                'before_data' => ['order' => $change['old_order']],
                'after_data' => ['order' => $change['new_order']],
                'reason' => 'Reordenación de tarea: ' . $change['title'],
                'ip_address' => $request->ip(),
            ]);
        }

        return response()->json([
            'success' => true,
            'changes' => count($changes),
        ]);
    }

    /**
     * Create a new task within a project group at a specific position.
     */
    public function store(Request $request, ProjectGroup $group)
    {
        $project = $group->project;

        $this->authorize('update', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_days' => 'required|integer|min:1|max:30',
            'is_required' => 'boolean',
            'is_deliverable' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        // If order not specified, place at the end
        if (!isset($validated['order'])) {
            $maxOrder = $group->tasks()->max('order');
            $validated['order'] = ($maxOrder ?? 0) + 1;
        } else {
            // Shift existing tasks to make room
            $group->tasks()
                ->where('order', '>=', $validated['order'])
                ->increment('order');
        }

        $task = $group->tasks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'duration_days' => $validated['duration_days'],
            'order' => $validated['order'],
            'is_required' => $validated['is_required'] ?? true,
            'is_deliverable' => $validated['is_deliverable'] ?? false,
            'status' => 'en_proceso',
        ]);

        // Calculate timeline for the project
        $this->recalculateTimeline($project);
        Artisan::call('tasks:update-statuses');

        // Audit event
        AuditEvent::create([
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'task_id' => $task->id,
            'action' => 'task_created',
            'entity_type' => 'task',
            'entity_id' => (string) $task->id,
            'before_data' => [],
            'after_data' => $task->toArray(),
            'reason' => 'Tarea creada desde editor de lista: ' . $task->title,
            'ip_address' => $request->ip(),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            $task->load('responsible');
            return response()->json([
                'success' => true,
                'task' => $task,
            ]);
        }

        return redirect()->route('projects.tasks.edit-list', $project)
            ->with('flash.banner', 'Tarea creada exitosamente.');
    }

    // =========================================================================
    // Group CRUD
    // =========================================================================

    /**
     * Create a new group within a project.
     */
    public function storeGroup(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_gate' => 'boolean',
            'insert_after_order' => 'nullable|integer|min:0',
        ]);

        // Determine the order for the new group
        $insertAfter = $validated['insert_after_order'] ?? null;

        if ($insertAfter !== null) {
            // Shift groups after the insertion point
            $project->groups()
                ->where('order', '>', $insertAfter)
                ->increment('order');
            $newOrder = $insertAfter + 1;
        } else {
            $maxOrder = $project->groups()->max('order');
            $newOrder = ($maxOrder ?? 0) + 1;
        }

        // First group is 'active', rest are 'locked' by default
        $existingCount = $project->groups()->count();
        $status = ($existingCount === 0 && $insertAfter === null) ? 'active' : 'locked';

        $group = $project->groups()->create([
            'name' => $validated['name'],
            'is_gate' => $validated['is_gate'] ?? false,
            'order' => $newOrder,
            'status' => $status,
            'gate_decision_role' => null,
            'unlocks_group_id' => null,
        ]);

        // Audit event
        AuditEvent::create([
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'action' => 'group_created',
            'entity_type' => 'project_group',
            'entity_id' => (string) $group->id,
            'before_data' => [],
            'after_data' => $group->toArray(),
            'reason' => 'Grupo creado desde editor de lista: ' . $group->name,
            'ip_address' => $request->ip(),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'group' => $group->fresh()->load('tasks'),
            ]);
        }

        return redirect()->route('projects.tasks.edit-list', $project)
            ->with('flash.banner', 'Grupo creado exitosamente.');
    }

    /**
     * Update a group's properties.
     */
    public function updateGroup(Request $request, ProjectGroup $group)
    {
        $project = $group->project;
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'is_gate' => 'sometimes|boolean',
            'status' => 'sometimes|in:active,locked,completed',
            'unlocks_group_id' => 'nullable|exists:project_groups,id',
        ]);

        $beforeData = $group->only(array_keys($validated));

        $group->update($validated);

        AuditEvent::create([
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'action' => 'group_updated',
            'entity_type' => 'project_group',
            'entity_id' => (string) $group->id,
            'before_data' => $beforeData,
            'after_data' => $validated,
            'reason' => 'Actualización de grupo desde editor de lista: ' . $group->name,
            'ip_address' => $request->ip(),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'group' => $group->fresh(),
            ]);
        }

        return redirect()->route('projects.tasks.edit-list', $project)
            ->with('flash.banner', 'Grupo actualizado exitosamente.');
    }

    /**
     * Delete a group and all its tasks.
     */
    public function deleteGroup(Request $request, ProjectGroup $group)
    {
        $project = $group->project;
        $this->authorize('update', $project);

        // Check if any tasks have assigned members
        $assignedCount = $group->tasks()->whereNotNull('responsible_user_id')->count();
        if ($assignedCount > 0) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el grupo porque tiene ' . $assignedCount . ' tarea(s) con responsable asignado. Reasigna o elimina las tareas primero.',
                ], 422);
            }
            return redirect()->route('projects.tasks.edit-list', $project)
                ->with('flash.banner', 'No se puede eliminar el grupo porque tiene tareas con responsables asignados.');
        }

        $groupName = $group->name;
        $groupId = $group->id;

        // Delete all tasks in the group
        $group->tasks()->delete();

        // Delete the group itself
        $group->delete();

        AuditEvent::create([
            'user_id' => Auth::id(),
            'project_id' => $project->id,
            'action' => 'group_deleted',
            'entity_type' => 'project_group',
            'entity_id' => (string) $groupId,
            'before_data' => ['name' => $groupName],
            'after_data' => [],
            'reason' => 'Grupo eliminado desde editor de lista: ' . $groupName,
            'ip_address' => $request->ip(),
        ]);

        // Recalculate timeline after group deletion
        $this->recalculateTimeline($project);
        Artisan::call('tasks:update-statuses');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Grupo eliminado exitosamente.',
            ]);
        }

        return redirect()->route('projects.tasks.edit-list', $project)
            ->with('flash.banner', 'Grupo y sus tareas eliminados exitosamente.');
    }

    /**
     * Reorder groups for a project.
     */
    public function reorderGroups(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'groups' => 'required|array',
            'groups.*.id' => 'required|exists:project_groups,id',
            'groups.*.order' => 'required|integer|min:0',
        ]);

        $changes = [];

        foreach ($validated['groups'] as $item) {
            $g = ProjectGroup::findOrFail($item['id']);

            // Ensure the group belongs to this project
            if ($g->project_id !== $project->id) {
                continue;
            }

            $oldOrder = $g->order;
            if ((int) $item['order'] !== $oldOrder) {
                $changes[] = [
                    'group_id' => $g->id,
                    'name' => $g->name,
                    'old_order' => $oldOrder,
                    'new_order' => (int) $item['order'],
                ];
            }

            $g->update(['order' => (int) $item['order']]);
        }

        // Recalculate timeline after reorder
        $this->recalculateTimeline($project);
        Artisan::call('tasks:update-statuses');

        foreach ($changes as $change) {
            AuditEvent::create([
                'user_id' => Auth::id(),
                'project_id' => $project->id,
                'action' => 'group_reordered',
                'entity_type' => 'project_group',
                'entity_id' => (string) $change['group_id'],
                'before_data' => ['order' => $change['old_order']],
                'after_data' => ['order' => $change['new_order']],
                'reason' => 'Reordenación de grupo: ' . $change['name'],
                'ip_address' => $request->ip(),
            ]);
        }

        return response()->json([
            'success' => true,
            'changes' => count($changes),
        ]);
    }

    /**
     * Recalculate timeline dates for the entire project.
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
