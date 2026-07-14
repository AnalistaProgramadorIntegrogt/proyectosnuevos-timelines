<?php

namespace App\Http\Controllers;

use App\Models\ProcessTemplate;
use App\Models\ProcessTemplateVersion;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     */
    public function index()
    {
        return view('projects.index');
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        $templates = ProcessTemplate::with('latestPublishedVersion')
            ->where('status', 'published')
            ->get();

        $users = User::all();

        return view('projects.create', compact('templates', 'users'));
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'process_template_version_id' => 'nullable|exists:process_template_versions,id',
        ]);

        $project = Project::create(array_merge($validated, [
            'owner_id' => auth()->id(),
            'default_approver_id' => $request->input('default_approver_id', auth()->id()),
            'lifecycle_status' => 'en_proceso',
        ]));

        if ($request->filled('process_template_version_id')) {
            $version = ProcessTemplateVersion::with('templateGroups.templateTasks.templateSubtasks')
                ->findOrFail($request->process_template_version_id);
            $this->copyTemplateToProject($version, $project);
        }

        return redirect()->route('projects.show', $project)
            ->with('flash.banner', 'Proyecto creado exitosamente.');
    }

    /**
     * Display the specified project with its timeline.
     */
    public function show(Project $project)
    {
        // Authorize view
        $this->authorize('view', $project);

        // Eager-load timeline data: groups ordered, tasks per group ordered, subtasks, users, deliverable versions, gate decisions
        $project->load([
            'owner',
            'groups' => function ($q) {
                $q->orderBy('order');
            },
            'groups.tasks' => function ($q) {
                $q->orderBy('order');
            },
            'groups.tasks.subtasks',
            'groups.tasks.responsibles',
            'groups.tasks.approvers',
            'groups.tasks.deliverableVersions',
            'groups.gateDecision',
            'groups.gateDecision.decisionMaker',
        ]);

        $timelineData = $project->groups;

        // Determine visibility rules for current user
        $visibilityRules = [];
        $canViewTaskDetails = false;
        $canEditTasks = false;

        $user = Auth::user();
        if ($user->id !== $project->owner_id) {
            $member = ProjectMember::where('project_id', $project->id)
                ->where('user_id', $user->id)
                ->first();

            if ($member && $member->project_role_id) {
                $member->load('role');
                $rules = $member->role->visibilityRules;
                foreach ($rules as $rule) {
                    $key = $rule->task_id ? 'task_' . $rule->task_id : 'group_' . $rule->group_id;
                    $visibilityRules[$key] = [
                        'can_view' => $rule->can_view,
                        'can_edit' => $rule->can_edit,
                    ];
                }
                $canViewTaskDetails = true;
                $canEditTasks = ($user->can('update', $project) || $member->role->can_add_edit_tasks);
            } else {
                // Not a member and not owner — shouldn't happen due to policy, but handle gracefully
                $canViewTaskDetails = false;
            }
        } else {
            // Owner can see everything
            $canViewTaskDetails = true;
            $canEditTasks = true;
        }

        return view('projects.timeline', compact('project', 'timelineData', 'visibilityRules', 'canViewTaskDetails', 'canEditTasks'));
    }

    /**
     * Display the specified project in a Roadmap (Gantt) view.
     */
    public function roadmap(Project $project)
    {
        $this->authorize('view', $project);

        $project->load([
            'owner',
            'groups' => function ($q) {
                $q->orderBy('order');
            },
            'groups.tasks' => function ($q) {
                $q->orderBy('order');
            },
            'groups.tasks.subtasks'
        ]);

        $showSubtasks = request()->boolean('show_subtasks');
        $taskIdFilter = request('task_id');

        $ganttTasks = [];
        
        foreach ($project->groups->sortBy('order') as $group) {
            foreach ($group->tasks->sortBy('order') as $task) {
                if ($taskIdFilter && $task->id != $taskIdFilter) continue;
                if (!$task->calculated_start_date || !$task->calculated_end_date) continue;
                
                // Determine progress based on subtasks if any, or status
                $progress = 0;
                if ($task->subtasks->count() > 0) {
                    $completedSubtasks = $task->subtasks->whereIn('status', ['entregado', 'aprobado'])->count();
                    $progress = round(($completedSubtasks / $task->subtasks->count()) * 100);
                } elseif (in_array($task->status, ['entregado', 'aprobado'])) {
                    $progress = 100;
                }

                $ganttTasks[] = [
                    'id' => 'task_' . $task->id,
                    'name' => $task->title,
                    'start' => $task->calculated_start_date->format('Y-m-d'),
                    'end' => $task->calculated_end_date->copy()->addDay()->format('Y-m-d'), // Frappe usually needs end date exclusive
                    'progress' => $progress,
                    'dependencies' => '', // Assuming sequential, or we could link previous tasks
                    'custom_class' => 'status-' . str_replace('_', '-', $task->status),
                    'task_id' => $task->id, // custom metadata
                ];

                if ($showSubtasks) {
                    foreach ($task->subtasks->sortBy('order') as $subtask) {
                        if (!$subtask->start_date || !$subtask->end_date) continue;

                        $subProgress = in_array($subtask->status, ['entregado', 'aprobado']) ? 100 : 0;

                        $ganttTasks[] = [
                            'id' => 'subtask_' . $subtask->id,
                            'name' => '↳ ' . $subtask->title,
                            'start' => $subtask->start_date->format('Y-m-d'),
                            'end' => $subtask->end_date->copy()->addDay()->format('Y-m-d'),
                            'progress' => $subProgress,
                            'dependencies' => 'task_' . $task->id,
                            'custom_class' => 'status-' . str_replace('_', '-', $subtask->status) . ' subtask-bar',
                            'task_id' => $task->id, // link back to parent task
                        ];
                    }
                }
            }
        }

        return view('projects.roadmap', compact('project', 'ganttTasks', 'showSubtasks', 'taskIdFilter'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'lifecycle_status' => 'nullable|string',
            'outcome' => 'nullable|string',
        ]);

        $project->update($validated);

        // If start_date changed, recalculate the full timeline
        if ($project->wasChanged('start_date') || $request->has('recalculate')) {
            $project->load('groups.tasks');
            $this->calculateTimeline($project);

            // Run the status update command to refresh pending/atrasado statuses
            \Illuminate\Support\Facades\Artisan::call('tasks:update-statuses');
        }

        return redirect()->route('projects.show', $project)
            ->with('flash.banner', 'Proyecto actualizado exitosamente.');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('flash.banner', 'Proyecto eliminado exitosamente.');
    }

    /**
     * Copy a template version's structure (groups, tasks, subtasks) into a project.
     */
    protected function copyTemplateToProject(ProcessTemplateVersion $version, Project $project)
    {
        $order = 1;
        foreach (($version->template_data['groups'] ?? []) as $groupData) {
            $projectGroup = $project->groups()->create([
                'name' => $groupData['name'],
                'order' => $order,
                'status' => $order === 1 ? 'active' : 'locked',
                'is_gate' => $groupData['is_gate'] ?? false,
            ]);

            $taskOrder = 1;
            foreach (($groupData['tasks'] ?? []) as $taskData) {
                $task = $projectGroup->tasks()->create([
                    'title' => $taskData['title'],
                    'description' => $taskData['description'] ?? '',
                    'order' => $taskOrder++,
                    'duration_days' => $taskData['duration_days'],
                    'is_required' => $taskData['is_required'] ?? true,
                    'is_deliverable' => $taskData['is_deliverable'] ?? false,
                    'status' => 'en_proceso',
                ]);

                if (!empty($taskData['responsible_users'])) {
                    $task->responsibles()->sync($taskData['responsible_users']);
                }
                if (!empty($taskData['approver_users'])) {
                    $task->approvers()->sync($taskData['approver_users']);
                }

                $subOrder = 1;
                foreach (($taskData['subtasks'] ?? []) as $subData) {
                    $subtask = $task->subtasks()->create([
                        'title' => $subData['title'],
                        'description' => $subData['description'] ?? '',
                        'duration_days' => $subData['duration_days'],
                        'is_deliverable' => $subData['is_deliverable'] ?? false,
                        'order' => $subOrder++,
                        'status' => 'en_proceso',
                    ]);

                    if (!empty($subData['responsible_users'])) {
                        $subtask->responsibles()->sync($subData['responsible_users']);
                    }
                    if (!empty($subData['approver_users'])) {
                        $subtask->approvers()->sync($subData['approver_users']);
                    }
                }
            }

            $order++;
        }

        // Calculate timeline for the project based on start_date
        $project->load('groups.tasks');
        $this->calculateTimeline($project);
        $project->save();
    }

    /**
     * Calculate start/end dates for each task based on project start_date.
     */
    protected function calculateTimeline(Project $project)
    {
        $currentDate = Carbon::parse($project->start_date);
        foreach ($project->groups->sortBy('order') as $group) {
            foreach ($group->tasks->sortBy('order') as $task) {
                $task->calculated_start_date = $currentDate->copy();
                $task->calculated_end_date = $currentDate->copy()->addDays($task->duration_days - 1);
                $task->save();
                
                if ($task->real_end_date && Carbon::parse($task->real_end_date)->isAfter($task->calculated_end_date)) {
                    $currentDate = Carbon::parse($task->real_end_date)->copy()->addDay();
                } else {
                    $currentDate = $task->calculated_end_date->copy()->addDay();
                }
            }
        }
    }
}
