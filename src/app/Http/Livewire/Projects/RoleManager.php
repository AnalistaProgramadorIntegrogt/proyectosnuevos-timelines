<?php

namespace App\Http\Livewire\Projects;

use App\Models\AuditEvent;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ProjectRole;
use App\Models\RoleVisibilityRule;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RoleManager extends Component
{
    public Project $project;

    public $editingRole = null;

    public $showCreateForm = false;

    // Form fields
    public $newRoleName = '';
    public $newRolePermissions = [
        'can_manage_settings' => false,
        'can_manage_roles' => false,
        'can_add_edit_tasks' => false,
        'can_reorder_tasks' => false,
        'can_upload_deliverables' => false,
        'can_approve_tasks' => false,
        'can_edit_status' => false,
        'can_view_audit' => false,
    ];

    // Edit fields
    public $editRoleId = null;
    public $editRoleName = '';
    public $editRolePermissions = [
        'can_manage_settings' => false,
        'can_manage_roles' => false,
        'can_add_edit_tasks' => false,
        'can_reorder_tasks' => false,
        'can_upload_deliverables' => false,
        'can_approve_tasks' => false,
        'can_edit_status' => false,
        'can_view_audit' => false,
    ];
    public $editVisibilityRules = [];

    public $projectGroups = [];
    public $showVisibilityConfig = false;

    public function toggleVisibilityConfig()
    {
        $this->showVisibilityConfig = !$this->showVisibilityConfig;
    }

    public function selectAllView()
    {
        foreach ($this->editVisibilityRules as $key => $rule) {
            $this->editVisibilityRules[$key]['can_view'] = true;
        }
    }

    public function deselectAllView()
    {
        foreach ($this->editVisibilityRules as $key => $rule) {
            $this->editVisibilityRules[$key]['can_view'] = false;
        }
    }

    public function selectAllEdit()
    {
        foreach ($this->editVisibilityRules as $key => $rule) {
            $this->editVisibilityRules[$key]['can_edit'] = true;
        }
    }

    public function deselectAllEdit()
    {
        foreach ($this->editVisibilityRules as $key => $rule) {
            $this->editVisibilityRules[$key]['can_edit'] = false;
        }
    }

    protected $rules = [
        'newRoleName' => 'required|string|max:100',
        'editRoleName' => 'required|string|max:100',
    ];

    protected $messages = [
        'newRoleName.required' => 'El nombre del rol es obligatorio.',
        'newRoleName.max' => 'El nombre no puede exceder los 100 caracteres.',
        'editRoleName.required' => 'El nombre del rol es obligatorio.',
        'editRoleName.max' => 'El nombre no puede exceder los 100 caracteres.',
    ];

    public function mount(Project $project)
    {
        $this->project = $project->load(['roles', 'groups.tasks']);
        $this->loadProjectGroups();
    }

    protected function loadProjectGroups()
    {
        $this->projectGroups = [];
        foreach ($this->project->groups->sortBy('order') as $group) {
            $tasks = [];
            foreach ($group->tasks->sortBy('order') as $task) {
                $tasks[] = [
                    'id' => $task->id,
                    'title' => $task->title,
                ];
            }
            $this->projectGroups[] = [
                'id' => $group->id,
                'name' => $group->name,
                'tasks' => $tasks,
                'is_gate' => $group->is_gate,
            ];
        }
    }

    protected function loadVisibilityRulesForEdit($roleId)
    {
        $this->editVisibilityRules = [];
        $existingRules = RoleVisibilityRule::where('project_role_id', $roleId)->get()->keyBy(function ($rule) {
            return $rule->task_id ? 'task_' . $rule->task_id : 'group_' . $rule->group_id;
        });

        foreach ($this->projectGroups as $group) {
            $groupKey = 'group_' . $group['id'];
            $this->editVisibilityRules[$groupKey] = [
                'group_id' => $group['id'],
                'task_id' => null,
                'can_view' => $existingRules->has($groupKey) ? $existingRules[$groupKey]->can_view : true,
                'can_edit' => $existingRules->has($groupKey) ? $existingRules[$groupKey]->can_edit : false,
                'is_group' => true,
                'name' => $group['name'],
            ];

            foreach ($group['tasks'] as $task) {
                $taskKey = 'task_' . $task['id'];
                $this->editVisibilityRules[$taskKey] = [
                    'group_id' => $group['id'],
                    'task_id' => $task['id'],
                    'can_view' => $existingRules->has($taskKey) ? $existingRules[$taskKey]->can_view : true,
                    'can_edit' => $existingRules->has($taskKey) ? $existingRules[$taskKey]->can_edit : false,
                    'is_group' => false,
                    'name' => $task['title'],
                ];
            }
        }
    }

    protected function saveVisibilityRules($roleId)
    {
        // Delete existing rules
        RoleVisibilityRule::where('project_role_id', $roleId)->delete();

        // Save new rules
        foreach ($this->editVisibilityRules as $key => $rule) {
            if ($rule['can_view'] === true && $rule['can_edit'] === false) {
                continue; // Skip default rules (view=true, edit=false) to keep table clean
            }
            RoleVisibilityRule::create([
                'project_role_id' => $roleId,
                'group_id' => $rule['group_id'],
                'task_id' => $rule['task_id'],
                'can_view' => $rule['can_view'],
                'can_edit' => $rule['can_edit'],
            ]);
        }
    }

    public function createRole()
    {
        $this->validateOnly('newRoleName');

        $role = $this->project->roles()->create([
            'name' => $this->newRoleName,
            ...$this->newRolePermissions,
        ]);

        // Audit log
        AuditEvent::create([
            'user_id' => Auth::id(),
            'project_id' => $this->project->id,
            'action' => 'role_created',
            'entity_type' => 'project_role',
            'entity_id' => (string) $role->id,
            'after_data' => [
                'name' => $this->newRoleName,
                'permissions' => $this->newRolePermissions,
            ],
            'reason' => 'Rol creado: ' . $this->newRoleName,
            'ip_address' => request()->ip(),
        ]);

        $this->newRoleName = '';
        $this->newRolePermissions = [
            'can_manage_settings' => false,
            'can_manage_roles' => false,
            'can_add_edit_tasks' => false,
            'can_reorder_tasks' => false,
            'can_upload_deliverables' => false,
            'can_approve_tasks' => false,
            'can_edit_status' => false,
            'can_view_audit' => false,
        ];
        $this->showCreateForm = false;
        $this->project->refresh();
        $this->project->load('roles');

        session()->flash('flash.banner', 'Rol "' . $role->name . '" creado exitosamente.');
    }

    public function saveNewVisibility()
    {
        if (!$this->editingRole) {
            return;
        }
        $this->saveVisibilityRules($this->editingRole);
        session()->flash('flash.banner', 'Reglas de visibilidad guardadas.');
    }

    public function startEdit($roleId)
    {
        $role = $this->project->roles()->findOrFail($roleId);
        $this->editRoleId = $role->id;
        $this->editRoleName = $role->name;
        $this->editRolePermissions = [
            'can_manage_settings' => (bool) $role->can_manage_settings,
            'can_manage_roles' => (bool) $role->can_manage_roles,
            'can_add_edit_tasks' => (bool) $role->can_add_edit_tasks,
            'can_reorder_tasks' => (bool) $role->can_reorder_tasks,
            'can_upload_deliverables' => (bool) $role->can_upload_deliverables,
            'can_approve_tasks' => (bool) $role->can_approve_tasks,
            'can_edit_status' => (bool) $role->can_edit_status,
            'can_view_audit' => (bool) $role->can_view_audit,
        ];

        // Load visibility rules for this role
        $this->loadVisibilityRulesForEdit($roleId);
        $this->showVisibilityConfig = false;
    }

    public function cancelEdit()
    {
        $this->editRoleId = null;
        $this->editRoleName = '';
        $this->editRolePermissions = [
            'can_manage_settings' => false,
            'can_manage_roles' => false,
            'can_add_edit_tasks' => false,
            'can_reorder_tasks' => false,
            'can_upload_deliverables' => false,
            'can_approve_tasks' => false,
            'can_edit_status' => false,
            'can_view_audit' => false,
        ];
    }

    public function updateRole()
    {
        $this->validateOnly('editRoleName');

        $role = $this->project->roles()->findOrFail($this->editRoleId);

        $oldData = [
            'name' => $role->name,
            'permissions' => $role->only([
                'can_manage_settings',
                'can_manage_roles',
                'can_add_edit_tasks',
                'can_reorder_tasks',
                'can_upload_deliverables',
                'can_approve_tasks',
                'can_edit_status',
                'can_view_audit',
            ]),
        ];

        $role->update([
            'name' => $this->editRoleName,
            ...$this->editRolePermissions,
        ]);

        // Audit log
        AuditEvent::create([
            'user_id' => Auth::id(),
            'project_id' => $this->project->id,
            'action' => 'role_updated',
            'entity_type' => 'project_role',
            'entity_id' => (string) $role->id,
            'before_data' => $oldData,
            'after_data' => [
                'name' => $this->editRoleName,
                'permissions' => $this->editRolePermissions,
            ],
            'reason' => 'Rol actualizado: ' . $this->editRoleName,
            'ip_address' => request()->ip(),
        ]);

        // Save visibility rules
        $this->saveVisibilityRules($role->id);

        $this->cancelEdit();
        $this->project->refresh();
        $this->project->load('roles');

        session()->flash('flash.banner', 'Rol "' . $role->name . '" actualizado exitosamente.');
    }

    public function deleteRole($roleId)
    {
        $role = $this->project->roles()->findOrFail($roleId);
        $roleName = $role->name;

        // Check if any members are assigned
        $memberCount = ProjectMember::where('project_role_id', $roleId)->count();
        if ($memberCount > 0) {
            session()->flash('flash.banner', 'No se puede eliminar el rol "' . $roleName . '" porque tiene ' . $memberCount . ' miembro(s) asignado(s).');
            session()->flash('flash.bannerStyle', 'danger');
            return;
        }

        // Audit log
        AuditEvent::create([
            'user_id' => Auth::id(),
            'project_id' => $this->project->id,
            'action' => 'role_deleted',
            'entity_type' => 'project_role',
            'entity_id' => (string) $role->id,
            'before_data' => [
                'name' => $roleName,
                'permissions' => $role->only([
                    'can_manage_settings',
                    'can_manage_roles',
                    'can_add_edit_tasks',
                    'can_reorder_tasks',
                    'can_upload_deliverables',
                    'can_approve_tasks',
                    'can_edit_status',
                    'can_view_audit',
                ]),
            ],
            'reason' => 'Rol eliminado: ' . $roleName,
            'ip_address' => request()->ip(),
        ]);

        $role->delete();

        if ($this->editRoleId === (int) $roleId) {
            $this->cancelEdit();
        }

        $this->project->refresh();
        $this->project->load('roles');

        session()->flash('flash.banner', 'Rol "' . $roleName . '" eliminado.');
    }

    public function render()
    {
        $this->project->loadMissing(['roles', 'groups.tasks']);
        $this->loadProjectGroups();

        return view('livewire.projects.role-manager', [
            'roles' => $this->project->roles,
            'groups' => $this->projectGroups,
        ]);
    }
}
