<?php

namespace App\Http\Livewire\Projects;

use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjectList extends Component
{
    public $projects;

    public function mount()
    {
        $user = Auth::user();
        $ownedIds = Project::where('owner_id', $user->id)->pluck('id');
        $memberIds = ProjectMember::where('user_id', $user->id)->pluck('project_id');
        $this->projects = Project::whereIn('id', $ownedIds->merge($memberIds)->unique())->get();
    }

    public function render()
    {
        return view('livewire.projects.project-list');
    }
}
