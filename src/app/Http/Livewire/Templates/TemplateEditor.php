<?php

namespace App\Http\Livewire\Templates;

use App\Models\ProcessTemplate;
use App\Models\ProcessTemplateVersion;
use App\Models\TemplateGroup;
use App\Models\TemplateTask;
use App\Models\TemplateSubtask;
use Livewire\Component;

class TemplateEditor extends Component
{
    public ProcessTemplate $template;
    public ?ProcessTemplateVersion $version = null;

    // Editing state
    public $editingGroupIndex = null;
    public $editingTaskGroupIndex = null;
    public $editingTaskIndex = null;
    public $editingSubtaskGroupIndex = null;
    public $editingSubtaskTaskIndex = null;
    public $editingSubtaskIndex = null;

    // Inline editing fields
    public $groupName = '';
    public $groupIsGate = false;

    public $taskTitle = '';
    public $taskDescription = '';
    public $taskDurationDays = 1;
    public $taskIsRequired = false;
    public $taskIsDeliverable = false;

    public $subtaskTitle = '';
    public $subtaskDescription = '';
    public $subtaskDurationDays = 1;

    // Version notes
    public $versionNotes = '';

    // Move tracking
    public $movedGroupIndex = null;
    public $moveDirection = null;

    protected $rules = [
        'versionNotes' => 'nullable|string|max:500',
    ];

    public function mount(ProcessTemplate $template, ?ProcessTemplateVersion $version = null)
    {
        $this->template = $template;

        if ($version && $version->exists && $version->process_template_id === $template->id) {
            $this->version = $version;
        } else {
            $this->version = $template->versions()->latest('version_number')->first();

            if (!$this->version) {
                // Create a draft version
                $this->version = $template->versions()->create([
                    'version_number' => 1,
                    'template_data' => ['groups' => []],
                    'status' => 'draft',
                    'notes' => 'Borrador inicial',
                ]);
            }
        }

        $this->versionNotes = $this->version->notes ?? '';
    }

    public function getGroupsProperty()
    {
        return $this->version->template_data['groups'] ?? [];
    }

    protected function setGroups($groups)
    {
        $data = $this->version->template_data ?? [];
        $data['groups'] = $groups;
        $this->version->template_data = $data;
    }

    // ── Group CRUD ──

    public function addGroup()
    {
        $this->validate(['groupName' => 'required|string|max:255']);

        $groups = $this->groups;
        $groups[] = [
            'name' => $this->groupName,
            'is_gate' => $this->groupIsGate,
            'order' => count($groups) + 1,
            'tasks' => [],
        ];
        $this->setGroups($groups);

        $this->resetGroupFields();
        $this->editingGroupIndex = null;
    }

    public function editGroup($index)
    {
        $groups = $this->groups;
        if (!isset($groups[$index])) return;

        $this->editingGroupIndex = $index;
        $this->groupName = $groups[$index]['name'];
        $this->groupIsGate = $groups[$index]['is_gate'] ?? false;
    }

    public function cancelEditGroup()
    {
        $this->editingGroupIndex = null;
        $this->resetGroupFields();
    }

    public function saveGroup($index)
    {
        $this->validate(['groupName' => 'required|string|max:255']);

        $groups = $this->groups;
        if (!isset($groups[$index])) return;

        $groups[$index]['name'] = $this->groupName;
        $groups[$index]['is_gate'] = $this->groupIsGate;
        $this->setGroups($groups);

        $this->editingGroupIndex = null;
        $this->resetGroupFields();
    }

    public function removeGroup($index)
    {
        $groups = $this->groups;
        if (!isset($groups[$index])) return;

        array_splice($groups, $index, 1);

        // Re-index orders
        $groups = array_values(array_map(function ($g, $i) {
            $g['order'] = $i + 1;
            return $g;
        }, $groups, array_keys($groups)));

        $this->setGroups($groups);
    }

    public function moveGroupUp($index)
    {
        if ($index === 0) return;
        $this->swapGroups($index, $index - 1);
    }

    public function moveGroupDown($index)
    {
        $groups = $this->groups;
        if ($index >= count($groups) - 1) return;
        $this->swapGroups($index, $index + 1);
    }

    protected function swapGroups($a, $b)
    {
        $groups = $this->groups;
        $temp = $groups[$a];
        $groups[$a] = $groups[$b];
        $groups[$b] = $temp;

        $groups = array_map(function ($g, $i) {
            $g['order'] = $i + 1;
            return $g;
        }, $groups, array_keys($groups));

        $this->setGroups($groups);
        $groups = array_values($groups);
    }

    protected function resetGroupFields()
    {
        $this->groupName = '';
        $this->groupIsGate = false;
    }

    // ── Task CRUD ──

    public function addTask($groupIndex)
    {
        $this->validate([
            'taskTitle' => 'required|string|max:255',
            'taskDurationDays' => 'required|integer|min:1',
        ]);

        $groups = $this->groups;
        if (!isset($groups[$groupIndex])) return;

        $tasks = $groups[$groupIndex]['tasks'] ?? [];
        $tasks[] = [
            'title' => $this->taskTitle,
            'description' => $this->taskDescription ?? '',
            'order' => count($tasks) + 1,
            'duration_days' => (int) $this->taskDurationDays,
            'is_required' => $this->taskIsRequired,
            'is_deliverable' => $this->taskIsDeliverable,
            'subtasks' => [],
        ];
        $groups[$groupIndex]['tasks'] = $tasks;
        $this->setGroups($groups);

        $this->resetTaskFields();
        $this->editingTaskGroupIndex = null;
        $this->editingTaskIndex = null;
    }

    public function editTask($groupIndex, $taskIndex)
    {
        $groups = $this->groups;
        if (!isset($groups[$groupIndex]['tasks'][$taskIndex])) return;

        $this->editingTaskGroupIndex = $groupIndex;
        $this->editingTaskIndex = $taskIndex;
        $task = $groups[$groupIndex]['tasks'][$taskIndex];
        $this->taskTitle = $task['title'];
        $this->taskDescription = $task['description'] ?? '';
        $this->taskDurationDays = $task['duration_days'] ?? 1;
        $this->taskIsRequired = $task['is_required'] ?? false;
        $this->taskIsDeliverable = $task['is_deliverable'] ?? false;
    }

    public function cancelEditTask()
    {
        $this->editingTaskGroupIndex = null;
        $this->editingTaskIndex = null;
        $this->resetTaskFields();
    }

    public function saveTask($groupIndex, $taskIndex)
    {
        $this->validate([
            'taskTitle' => 'required|string|max:255',
            'taskDurationDays' => 'required|integer|min:1',
        ]);

        $groups = $this->groups;
        if (!isset($groups[$groupIndex]['tasks'][$taskIndex])) return;

        $groups[$groupIndex]['tasks'][$taskIndex]['title'] = $this->taskTitle;
        $groups[$groupIndex]['tasks'][$taskIndex]['description'] = $this->taskDescription ?? '';
        $groups[$groupIndex]['tasks'][$taskIndex]['duration_days'] = (int) $this->taskDurationDays;
        $groups[$groupIndex]['tasks'][$taskIndex]['is_required'] = $this->taskIsRequired;
        $groups[$groupIndex]['tasks'][$taskIndex]['is_deliverable'] = $this->taskIsDeliverable;
        $this->setGroups($groups);

        $this->editingTaskGroupIndex = null;
        $this->editingTaskIndex = null;
        $this->resetTaskFields();
    }

    public function removeTask($groupIndex, $taskIndex)
    {
        $groups = $this->groups;
        if (!isset($groups[$groupIndex]['tasks'][$taskIndex])) return;

        array_splice($groups[$groupIndex]['tasks'], $taskIndex, 1);

        // Re-index orders
        $groups[$groupIndex]['tasks'] = array_values(array_map(function ($t, $i) {
            $t['order'] = $i + 1;
            return $t;
        }, $groups[$groupIndex]['tasks'], array_keys($groups[$groupIndex]['tasks'])));

        $this->setGroups($groups);
    }

    public function moveTaskUp($groupIndex, $taskIndex)
    {
        if ($taskIndex === 0) return;
        $this->swapTasks($groupIndex, $taskIndex, $taskIndex - 1);
    }

    public function moveTaskDown($groupIndex, $taskIndex)
    {
        $groups = $this->groups;
        if (!isset($groups[$groupIndex]['tasks'][$taskIndex + 1])) return;
        $this->swapTasks($groupIndex, $taskIndex, $taskIndex + 1);
    }

    protected function swapTasks($groupIndex, $a, $b)
    {
        $groups = $this->groups;
        $tasks = $groups[$groupIndex]['tasks'];
        $temp = $tasks[$a];
        $tasks[$a] = $tasks[$b];
        $tasks[$b] = $temp;

        $tasks = array_map(function ($t, $i) {
            $t['order'] = $i + 1;
            return $t;
        }, $tasks, array_keys($tasks));

        $groups[$groupIndex]['tasks'] = array_values($tasks);
        $this->setGroups($groups);
    }

    protected function resetTaskFields()
    {
        $this->taskTitle = '';
        $this->taskDescription = '';
        $this->taskDurationDays = 1;
        $this->taskIsRequired = false;
        $this->taskIsDeliverable = false;
    }

    // ── Subtask CRUD ──

    public function addSubtask($groupIndex, $taskIndex)
    {
        $this->validate([
            'subtaskTitle' => 'required|string|max:255',
            'subtaskDurationDays' => 'required|integer|min:1',
        ]);

        $groups = $this->groups;
        if (!isset($groups[$groupIndex]['tasks'][$taskIndex])) return;

        $subtasks = $groups[$groupIndex]['tasks'][$taskIndex]['subtasks'] ?? [];
        $subtasks[] = [
            'title' => $this->subtaskTitle,
            'description' => $this->subtaskDescription ?? '',
            'duration_days' => (int) $this->subtaskDurationDays,
            'order' => count($subtasks) + 1,
        ];
        $groups[$groupIndex]['tasks'][$taskIndex]['subtasks'] = $subtasks;
        $this->setGroups($groups);

        $this->resetSubtaskFields();
        $this->editingSubtaskGroupIndex = null;
        $this->editingSubtaskTaskIndex = null;
        $this->editingSubtaskIndex = null;
    }

    public function editSubtask($groupIndex, $taskIndex, $subtaskIndex)
    {
        $groups = $this->groups;
        if (!isset($groups[$groupIndex]['tasks'][$taskIndex]['subtasks'][$subtaskIndex])) return;

        $this->editingSubtaskGroupIndex = $groupIndex;
        $this->editingSubtaskTaskIndex = $taskIndex;
        $this->editingSubtaskIndex = $subtaskIndex;
        $sub = $groups[$groupIndex]['tasks'][$taskIndex]['subtasks'][$subtaskIndex];
        $this->subtaskTitle = $sub['title'];
        $this->subtaskDescription = $sub['description'] ?? '';
        $this->subtaskDurationDays = $sub['duration_days'] ?? 1;
    }

    public function cancelEditSubtask()
    {
        $this->editingSubtaskGroupIndex = null;
        $this->editingSubtaskTaskIndex = null;
        $this->editingSubtaskIndex = null;
        $this->resetSubtaskFields();
    }

    public function saveSubtask($groupIndex, $taskIndex, $subtaskIndex)
    {
        $this->validate([
            'subtaskTitle' => 'required|string|max:255',
            'subtaskDurationDays' => 'required|integer|min:1',
        ]);

        $groups = $this->groups;
        if (!isset($groups[$groupIndex]['tasks'][$taskIndex]['subtasks'][$subtaskIndex])) return;

        $groups[$groupIndex]['tasks'][$taskIndex]['subtasks'][$subtaskIndex]['title'] = $this->subtaskTitle;
        $groups[$groupIndex]['tasks'][$taskIndex]['subtasks'][$subtaskIndex]['description'] = $this->subtaskDescription ?? '';
        $groups[$groupIndex]['tasks'][$taskIndex]['subtasks'][$subtaskIndex]['duration_days'] = (int) $this->subtaskDurationDays;
        $this->setGroups($groups);

        $this->editingSubtaskGroupIndex = null;
        $this->editingSubtaskTaskIndex = null;
        $this->editingSubtaskIndex = null;
        $this->resetSubtaskFields();
    }

    public function removeSubtask($groupIndex, $taskIndex, $subtaskIndex)
    {
        $groups = $this->groups;
        if (!isset($groups[$groupIndex]['tasks'][$taskIndex]['subtasks'][$subtaskIndex])) return;

        array_splice($groups[$groupIndex]['tasks'][$taskIndex]['subtasks'], $subtaskIndex, 1);

        // Re-index orders
        $groups[$groupIndex]['tasks'][$taskIndex]['subtasks'] = array_values(array_map(function ($s, $i) {
            $s['order'] = $i + 1;
            return $s;
        }, $groups[$groupIndex]['tasks'][$taskIndex]['subtasks'], array_keys($groups[$groupIndex]['tasks'][$taskIndex]['subtasks'])));

        $this->setGroups($groups);
    }

    public function moveSubtaskUp($groupIndex, $taskIndex, $subtaskIndex)
    {
        if ($subtaskIndex === 0) return;
        $this->swapSubtasks($groupIndex, $taskIndex, $subtaskIndex, $subtaskIndex - 1);
    }

    public function moveSubtaskDown($groupIndex, $taskIndex, $subtaskIndex)
    {
        $groups = $this->groups;
        $subtasks = $groups[$groupIndex]['tasks'][$taskIndex]['subtasks'] ?? [];
        if ($subtaskIndex >= count($subtasks) - 1) return;
        $this->swapSubtasks($groupIndex, $taskIndex, $subtaskIndex, $subtaskIndex + 1);
    }

    protected function swapSubtasks($groupIndex, $taskIndex, $a, $b)
    {
        $groups = $this->groups;
        $subtasks = $groups[$groupIndex]['tasks'][$taskIndex]['subtasks'];
        $temp = $subtasks[$a];
        $subtasks[$a] = $subtasks[$b];
        $subtasks[$b] = $temp;

        $subtasks = array_map(function ($s, $i) {
            $s['order'] = $i + 1;
            return $s;
        }, $subtasks, array_keys($subtasks));

        $groups[$groupIndex]['tasks'][$taskIndex]['subtasks'] = array_values($subtasks);
        $this->setGroups($groups);
    }

    protected function resetSubtaskFields()
    {
        $this->subtaskTitle = '';
        $this->subtaskDescription = '';
        $this->subtaskDurationDays = 1;
    }

    // ── Persistence ──

    public function saveVersion()
    {
        $this->version->notes = $this->versionNotes;
        $this->version->save();

        $this->syncRelationalTables();

        session()->flash('flash.banner', 'Versión guardada exitosamente.');
    }

    public function publishVersion()
    {
        $this->saveVersion();
        $this->version->update(['status' => 'published']);

        // If this is a draft state on the template, update it
        if ($this->template->status === 'draft') {
            $this->template->update(['status' => 'published']);
        }

        session()->flash('flash.banner', 'Versión publicada exitosamente.');
    }

    /**
     * Sync the relational tables (template_groups, template_tasks, template_subtasks)
     * from the template_data JSON structure.
     */
    protected function syncRelationalTables()
    {
        $versionId = $this->version->id;
        $groups = $this->groups;

        // Track IDs that were mapped from old data via order/name matching
        $existingGroups = TemplateGroup::where('process_template_version_id', $versionId)->get()->keyBy('id');
        $newGroupIds = [];
        $groupOrderMap = [];

        foreach ($groups as $gIndex => $groupData) {
            // Try to match existing group by order
            $matchedGroup = $existingGroups->first(function ($eg) use ($groupData) {
                return $eg->order === $groupData['order'];
            });

            if ($matchedGroup) {
                $group = $matchedGroup;
            } else {
                $group = new TemplateGroup();
                $group->process_template_version_id = $versionId;
            }

            $group->name = $groupData['name'];
            $group->order = $groupData['order'];
            $group->is_gate = $groupData['is_gate'] ?? false;
            $group->save();
            $newGroupIds[] = $group->id;
            $groupOrderMap[$gIndex] = $group->id;

            // Sync tasks
            $existingTasks = TemplateTask::where('template_group_id', $group->id)->get()->keyBy('id');
            $newTaskIds = [];

            foreach (($groupData['tasks'] ?? []) as $tIndex => $taskData) {
                $matchedTask = $existingTasks->first(function ($et) use ($taskData) {
                    return $et->order === $taskData['order'];
                });

                if ($matchedTask) {
                    $task = $matchedTask;
                } else {
                    $task = new TemplateTask();
                    $task->template_group_id = $group->id;
                }

                $task->title = $taskData['title'];
                $task->description = $taskData['description'] ?? '';
                $task->order = $taskData['order'];
                $task->duration_days = $taskData['duration_days'] ?? 1;
                $task->is_required = $taskData['is_required'] ?? false;
                $task->is_deliverable = $taskData['is_deliverable'] ?? false;
                $task->save();
                $newTaskIds[] = $task->id;

                // Sync subtasks
                $existingSubtasks = TemplateSubtask::where('template_task_id', $task->id)->get()->keyBy('id');
                $newSubtaskIds = [];

                foreach (($taskData['subtasks'] ?? []) as $sIndex => $subtaskData) {
                    $matchedSubtask = $existingSubtasks->first(function ($es) use ($subtaskData) {
                        return $es->order === $subtaskData['order'];
                    });

                    if ($matchedSubtask) {
                        $subtask = $matchedSubtask;
                    } else {
                        $subtask = new TemplateSubtask();
                        $subtask->template_task_id = $task->id;
                    }

                    $subtask->title = $subtaskData['title'];
                    $subtask->description = $subtaskData['description'] ?? '';
                    $subtask->duration_days = $subtaskData['duration_days'] ?? 1;
                    $subtask->order = $subtaskData['order'];
                    $subtask->save();
                    $newSubtaskIds[] = $subtask->id;
                }

                // Remove subtasks that no longer exist
                TemplateSubtask::where('template_task_id', $task->id)
                    ->whereNotIn('id', $newSubtaskIds)
                    ->delete();
            }

            // Remove tasks that no longer exist
            TemplateTask::where('template_group_id', $group->id)
                ->whereNotIn('id', $newTaskIds)
                ->delete();
        }

        // Remove groups that no longer exist
        TemplateGroup::where('process_template_version_id', $versionId)
            ->whereNotIn('id', $newGroupIds)
            ->delete();
    }

    public function render()
    {
        return view('livewire.templates.template-editor', [
            'groups' => $this->groups,
        ]);
    }
}
