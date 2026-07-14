<?php

namespace App\Http\Livewire\Tasks;

use App\Models\ApprovalDecision;
use App\Models\AuditEvent;
use App\Models\ProjectMember;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ApprovalPanel extends Component
{
    public Task $task;
    public ?\App\Models\Subtask $subtask = null;

    public $reason = '';

    public $showRejectModal = false;

    protected $rules = [
        'reason' => 'required|string|max:5000',
    ];

    protected $messages = [
        'reason.required' => 'Debes proporcionar una razón para el rechazo.',
        'reason.max' => 'La razón no puede exceder los 5000 caracteres.',
    ];

    public function mount(Task $task, $subtask = null)
    {
        $this->task = $task;
        if ($subtask) {
            $this->subtask = $subtask;
        } else {
            $this->task->load([
                'submissions',
                'submissions.submitter',
                'submissions.deliverableVersion',
                'submissions.approvalDecision',
                'submissions.approvalDecision.approver',
                'projectGroup',
                'projectGroup.project',
                'approvers',
            ]);
        }
    }

    public function canApprove(): bool
    {
        $user = Auth::user();
        $projectId = $this->task->projectGroup->project_id;

        // Owner can always approve
        $project = $this->task->projectGroup->project;
        if ($project->owner_id === $user->id) {
            return true;
        }

        // Explicit approvers can approve
        $target = $this->getTarget();
        if ($target->approvers->contains('id', $user->id)) {
            return true;
        }

        // Check member role permission
        $member = ProjectMember::where('project_id', $projectId)
            ->where('user_id', $user->id)
            ->first();

        if ($member && $member->role) {
            return $member->role->can_approve_tasks;
        }

        return false;
    }

    protected function getTarget()
    {
        return $this->subtask ?? $this->task;
    }

    public function getLatestSubmissionProperty()
    {
        $query = TaskSubmission::with(['submitter', 'deliverableVersion', 'approvalDecision.approver'])
            ->orderBy('created_at', 'desc');

        if ($this->subtask) {
            $query->where('subtask_id', $this->subtask->id);
        } else {
            $query->where('task_id', $this->task->id)->whereNull('subtask_id');
        }

        return $query->first();
    }

    public function approve()
    {
        if (!$this->canApprove()) {
            session()->flash('flash.banner', 'No tienes permiso para aprobar entregables.');
            session()->flash('flash.bannerStyle', 'danger');
            return;
        }

        $latestSubmission = $this->latestSubmission;

        if (!$latestSubmission) {
            session()->flash('flash.banner', 'No hay entregables pendientes por revisar.');
            session()->flash('flash.bannerStyle', 'danger');
            return;
        }

        if ($latestSubmission->approvalDecision) {
            session()->flash('flash.banner', 'Este entregable ya ha sido revisado.');
            session()->flash('flash.bannerStyle', 'warning');
            return;
        }

        $user = Auth::user();
        $target = $this->getTarget();

        // Create approval decision
        $decision = $latestSubmission->approvalDecision()->create([
            'approver_id' => $user->id,
            'decision' => 'approved',
            'note' => null,
        ]);

        // Update target status
        $target->update(['status' => 'aprobado']);

        // Audit log
        AuditEvent::create([
            'user_id' => $user->id,
            'project_id' => $this->task->projectGroup->project_id,
            'task_id' => $this->task->id,
            'action' => 'task_approved',
            'entity_type' => 'approval_decision',
            'entity_id' => (string) $decision->id,
            'after_data' => [
                'submission_id' => $latestSubmission->id,
                'decision' => 'approved',
                'subtask_id' => $this->subtask ? $this->subtask->id : null,
            ],
            'reason' => 'Aprobación de entregable para ' . ($this->subtask ? 'subtarea: ' . $this->subtask->title : 'la tarea: ' . $this->task->title),
            'ip_address' => request()->ip(),
        ]);

        $target->refresh();
        if (!$this->subtask) {
            $this->task->load([
                'submissions.submitter',
                'submissions.deliverableVersion',
                'submissions.approvalDecision',
                'submissions.approvalDecision.approver',
            ]);
        }

        session()->flash('flash.banner', 'Entregable aprobado exitosamente.');
    }

    public function reject()
    {
        $this->validate();

        if (!$this->canApprove()) {
            session()->flash('flash.banner', 'No tienes permiso para rechazar entregables.');
            session()->flash('flash.bannerStyle', 'danger');
            return;
        }

        $latestSubmission = $this->latestSubmission;

        if (!$latestSubmission) {
            session()->flash('flash.banner', 'No hay entregables pendientes por revisar.');
            session()->flash('flash.bannerStyle', 'danger');
            return;
        }

        if ($latestSubmission->approvalDecision) {
            session()->flash('flash.banner', 'Este entregable ya ha sido revisado.');
            session()->flash('flash.bannerStyle', 'warning');
            return;
        }

        $user = Auth::user();
        $target = $this->getTarget();

        // Create rejection decision
        $decision = $latestSubmission->approvalDecision()->create([
            'approver_id' => $user->id,
            'decision' => 'rejected',
            'note' => $this->reason,
        ]);

        // Update target status
        $target->update(['status' => 'rechazado']);

        // Audit log
        AuditEvent::create([
            'user_id' => $user->id,
            'project_id' => $this->task->projectGroup->project_id,
            'task_id' => $this->task->id,
            'action' => 'task_rejected',
            'entity_type' => 'approval_decision',
            'entity_id' => (string) $decision->id,
            'after_data' => [
                'submission_id' => $latestSubmission->id,
                'decision' => 'rejected',
                'reason' => $this->reason,
                'subtask_id' => $this->subtask ? $this->subtask->id : null,
            ],
            'reason' => 'Rechazo de entregable para ' . ($this->subtask ? 'subtarea: ' . $this->subtask->title : 'la tarea: ' . $this->task->title) . ' — Razón: ' . $this->reason,
            'ip_address' => request()->ip(),
        ]);

        $this->showRejectModal = false;
        $this->reason = '';

        $target->refresh();
        if (!$this->subtask) {
            $this->task->load([
                'submissions.submitter',
                'submissions.deliverableVersion',
                'submissions.approvalDecision',
                'submissions.approvalDecision.approver',
            ]);
        }

        session()->flash('flash.banner', 'Entregable rechazado.');
    }

    public function render()
    {
        $latestSubmission = $this->latestSubmission;
        
        $query = TaskSubmission::with(['submitter', 'deliverableVersion', 'approvalDecision.approver'])
            ->whereHas('approvalDecision')
            ->orderBy('created_at', 'desc');

        if ($this->subtask) {
            $query->where('subtask_id', $this->subtask->id);
        } else {
            $query->where('task_id', $this->task->id)->whereNull('subtask_id');
        }

        $approvalHistory = $query->get();

        return view('livewire.tasks.approval-panel', [
            'latestSubmission' => $latestSubmission,
            'approvalHistory' => $approvalHistory,
            'canApprove' => $this->canApprove(),
            'isSubtask' => $this->subtask !== null,
        ]);
    }
}
