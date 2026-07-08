<?php

namespace App\Policies;

use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, $project)
    {
        $member = ProjectMember::where('project_id', $project->id)->where('user_id', $user->id)->first();
        if ($member) {
            return true;
        }

        return $project->owner_id === $user->id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, $task)
    {
        $project = $task->project;
        $member = ProjectMember::where('project_id', $project->id)->where('user_id', $user->id)->first();
        if ($member) {
            return true;
        }

        return $project->owner_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, $project)
    {
        $member = ProjectMember::where('project_id', $project->id)->where('user_id', $user->id)->first();
        if ($member && $member->role->can_create_tasks) {
            return true;
        }

        return $project->owner_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, $task)
    {
        $project = $task->project;
        $member = ProjectMember::where('project_id', $project->id)->where('user_id', $user->id)->first();
        if ($member && $member->role->can_manage_tasks) {
            return true;
        }

        return $project->owner_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, $task)
    {
        $project = $task->project;
        $member = ProjectMember::where('project_id', $project->id)->where('user_id', $user->id)->first();
        if ($member && $member->role->can_manage_tasks) {
            return true;
        }

        return $project->owner_id === $user->id;
    }
}
