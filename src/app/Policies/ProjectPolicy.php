<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
    }

    public function view(User $user, Project $project)
    {
        $member = ProjectMember::where('project_id', $project->id)->where('user_id', $user->id)->first();
        if ($member) {
            return true;
        }

        return $project->owner_id === $user->id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Project $project)
    {
        $member = ProjectMember::where('project_id', $project->id)->where('user_id', $user->id)->first();
        if ($member && $member->role->can_manage_settings) {
            return true;
        }

        return $project->owner_id === $user->id;
    }

    public function manageRoles(User $user, Project $project)
    {
        $member = ProjectMember::where('project_id', $project->id)->where('user_id', $user->id)->first();
        if ($member && $member->role->can_manage_roles) {
            return true;
        }

        return $project->owner_id === $user->id;
    }

    public function delete(User $user, Project $project)
    {
        // Only owner or system admin
        return $project->owner_id === $user->id || $user->hasRole('admin');
    }
}
