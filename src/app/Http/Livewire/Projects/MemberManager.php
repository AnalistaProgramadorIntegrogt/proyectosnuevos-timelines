<?php

namespace App\Http\Livewire\Projects;

use App\Models\AuditEvent;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MemberManager extends Component
{
    public Project $project;

    public $email = '';

    public $selectedRole = '';

    public $confirmingRemove = null;

    protected $rules = [
        'email' => 'required|email|max:255',
        'selectedRole' => 'required|exists:project_roles,id',
    ];

    protected $messages = [
        'email.required' => 'Debes ingresar un correo electrónico.',
        'email.email' => 'Ingresa un correo electrónico válido.',
        'email.max' => 'El correo no puede exceder los 255 caracteres.',
        'selectedRole.required' => 'Debes seleccionar un rol.',
        'selectedRole.exists' => 'El rol seleccionado no es válido.',
    ];

    public function mount(Project $project)
    {
        $this->project = $project->load(['members.user', 'members.role', 'roles']);
        $this->selectedRole = $this->project->roles->first()?->id ?? '';
    }

    public function addMember()
    {
        $this->validate();

        // Find user by email
        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('email', 'No existe un usuario registrado con ese correo electrónico.');
            return;
        }

        // Check if already a member
        $existing = ProjectMember::where('project_id', $this->project->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $this->addError('email', 'Este usuario ya es miembro del proyecto.');
            return;
        }

        // Check if user is project owner
        if ($this->project->owner_id === $user->id) {
            $this->addError('email', 'El propietario del proyecto no puede ser agregado como miembro.');
            return;
        }

        $authUser = Auth::user();

        // Add member
        $member = $this->project->members()->create([
            'user_id' => $user->id,
            'project_role_id' => $this->selectedRole,
        ]);

        // Audit log
        AuditEvent::create([
            'user_id' => $authUser->id,
            'project_id' => $this->project->id,
            'action' => 'member_added',
            'entity_type' => 'project_member',
            'entity_id' => (string) $member->id,
            'after_data' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'role_id' => $this->selectedRole,
                'role_name' => $this->project->roles->find($this->selectedRole)?->name,
            ],
            'reason' => 'Miembro agregado al proyecto: ' . $user->name . ' (' . $user->email . ')',
            'ip_address' => request()->ip(),
        ]);

        $this->email = '';
        $this->selectedRole = $this->project->roles->first()?->id ?? '';
        $this->project->refresh();
        $this->project->load(['members.user', 'members.role']);

        session()->flash('flash.banner', 'Miembro agregado exitosamente: ' . $user->name);
    }

    public function confirmRemoveMember($memberId)
    {
        $this->confirmingRemove = $memberId;
    }

    public function cancelRemove()
    {
        $this->confirmingRemove = null;
    }

    public function removeMember(ProjectMember $member)
    {
        $authUser = Auth::user();

        $userName = $member->user?->name ?? 'Usuario desconocido';

        // Audit log before removing
        AuditEvent::create([
            'user_id' => $authUser->id,
            'project_id' => $this->project->id,
            'action' => 'member_removed',
            'entity_type' => 'project_member',
            'entity_id' => (string) $member->id,
            'before_data' => [
                'user_id' => $member->user_id,
                'user_name' => $userName,
                'user_email' => $member->user?->email,
                'role_id' => $member->project_role_id,
                'role_name' => $member->role?->name,
            ],
            'reason' => 'Miembro eliminado del proyecto: ' . $userName,
            'ip_address' => request()->ip(),
        ]);

        $member->delete();

        $this->confirmingRemove = null;
        $this->project->refresh();
        $this->project->load(['members.user', 'members.role']);

        session()->flash('flash.banner', 'Miembro eliminado: ' . $userName);
    }

    public function changeRole(ProjectMember $member, $roleId)
    {
        $authUser = Auth::user();

        $oldRoleName = $member->role?->name;
        $newRole = $this->project->roles()->find($roleId);

        if (!$newRole) {
            session()->flash('flash.banner', 'El rol seleccionado no es válido.');
            session()->flash('flash.bannerStyle', 'danger');
            return;
        }

        $member->update(['project_role_id' => $roleId]);

        // Audit log
        AuditEvent::create([
            'user_id' => $authUser->id,
            'project_id' => $this->project->id,
            'action' => 'member_role_changed',
            'entity_type' => 'project_member',
            'entity_id' => (string) $member->id,
            'before_data' => [
                'role_id' => $member->getOriginal('project_role_id'),
                'role_name' => $oldRoleName,
            ],
            'after_data' => [
                'role_id' => $roleId,
                'role_name' => $newRole->name,
            ],
            'reason' => 'Rol cambiado de "' . $oldRoleName . '" a "' . $newRole->name . '" para: ' . ($member->user?->name ?? ''),
            'ip_address' => request()->ip(),
        ]);

        $this->project->refresh();
        $this->project->load(['members.user', 'members.role']);

        session()->flash('flash.banner', 'Rol actualizado exitosamente.');
    }

    public function render()
    {
        $this->project->loadMissing(['members.user', 'members.role', 'roles']);

        return view('livewire.projects.member-manager', [
            'members' => $this->project->members,
            'roles' => $this->project->roles,
        ]);
    }
}
