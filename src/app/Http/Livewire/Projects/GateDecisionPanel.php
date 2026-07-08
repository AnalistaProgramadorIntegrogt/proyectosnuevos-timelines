<?php

namespace App\Http\Livewire\Projects;

use App\Models\AuditEvent;
use App\Models\GateDecision;
use App\Models\ProjectGroup;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GateDecisionPanel extends Component
{
    public ProjectGroup $group;

    public $notes = '';

    protected $rules = [
        'notes' => 'nullable|string|max:5000',
    ];

    protected $messages = [
        'notes.max' => 'Las notas no pueden exceder los 5000 caracteres.',
    ];

    public function mount(ProjectGroup $group)
    {
        $this->group = $group->load([
            'gateDecision.decisionMaker',
            'tasks',
        ]);
    }

    public function makeDecision($outcome)
    {
        $this->validate();

        // Prevent duplicate decisions
        if ($this->group->relationLoaded('gateDecision') && $this->group->gateDecision) {
            session()->flash('flash.banner', 'Este gate ya ha sido decidido.');
            session()->flash('flash.bannerStyle', 'warning');
            return;
        }

        $user = Auth::user();

        // Create the gate decision record
        $decision = GateDecision::create([
            'project_group_id' => $this->group->id,
            'decision_maker_id' => $user->id,
            'outcome' => $outcome,
            'notes' => $this->notes ?: null,
        ]);

        if ($outcome === 'viable') {
            // Mark this gate group as completed (viable)
            $this->group->update(['status' => 'completed_viable']);

            // Find the next group by order and unlock it
            $nextGroup = ProjectGroup::where('project_id', $this->group->project_id)
                ->where('order', $this->group->order + 1)
                ->first();

            if ($nextGroup) {
                $nextGroup->update(['status' => 'active']);
            }

            // Audit log
            AuditEvent::create([
                'user_id' => $user->id,
                'project_id' => $this->group->project_id,
                'action' => 'gate_viable',
                'entity_type' => 'gate_decision',
                'entity_id' => (string) $decision->id,
                'after_data' => [
                    'group_id' => $this->group->id,
                    'group_name' => $this->group->name,
                    'outcome' => 'viable',
                    'notes' => $this->notes,
                ],
                'reason' => 'Gate aprobado (viable) para el grupo: ' . $this->group->name,
                'ip_address' => request()->ip(),
            ]);

            session()->flash('flash.banner', '✅ Gate aprobado. El siguiente grupo se ha desbloqueado.');
        } else {
            // nonviable — mark gate as rejected
            $this->group->update(['status' => 'gate_rejected']);

            // Lock all subsequent groups
            ProjectGroup::where('project_id', $this->group->project_id)
                ->where('order', '>', $this->group->order)
                ->update(['status' => 'locked']);

            // Update project lifecycle status and outcome
            $project = $this->group->project;
            $project->update([
                'lifecycle_status' => 'finished',
                'outcome' => 'nonviable',
            ]);

            // Audit log
            AuditEvent::create([
                'user_id' => $user->id,
                'project_id' => $this->group->project_id,
                'action' => 'gate_nonviable',
                'entity_type' => 'gate_decision',
                'entity_id' => (string) $decision->id,
                'after_data' => [
                    'group_id' => $this->group->id,
                    'group_name' => $this->group->name,
                    'outcome' => 'nonviable',
                    'notes' => $this->notes,
                    'project_lifecycle_status' => 'finished',
                    'project_outcome' => 'nonviable',
                ],
                'reason' => 'Gate rechazado (no viable) para el grupo: ' . $this->group->name . ' — El proyecto se ha marcado como no viable.',
                'ip_address' => request()->ip(),
            ]);

            session()->flash('flash.banner', '❌ Gate rechazado. El proyecto ha sido marcado como no viable.');
        }

        // Refresh the group to reflect changes
        $this->group->refresh();
        $this->group->load([
            'gateDecision.decisionMaker',
            'tasks',
        ]);
    }

    public function render()
    {
        $allTasksApproved = $this->group->tasks->every(fn ($task) => $task->status === 'aprobado');
        $hasDecision = $this->group->relationLoaded('gateDecision') && $this->group->gateDecision;

        return view('livewire.projects.gate-decision-panel', [
            'allTasksApproved' => $allTasksApproved,
            'hasDecision' => $hasDecision,
        ]);
    }
}
