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

    public $reason = '';

    public $showRejectModal = false;

    protected $rules = [
        'reason' => 'required|string|max:5000',
    ];

    protected $messages = [
        'reason.required' => 'Debes proporcionar una razón para el rechazo.',
        'reason.max' => 'La razón no puede exceder los 5000 caracteres.',
    ];

    public function mount(Task $task)
    {
        $this->task = $task->load([
            'submissions',
            'submissions.submitter',
            'submissions.deliverableVersion',
            'submissions.approvalDecision',
            'submissions.approvalDecision.approver',
            'projectGroup',
            'projectGroup.project',
            'explicitApprover',
        ]);
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

        // Explicit approver can approve
        if ($this->task->explicit_approver_id === $user->id) {
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

    public function getLatestSubmissionProperty()
    {
        return $this->task->submissions()
            ->with(['submitter', 'deliverableVersion', 'approvalDecision.approver'])
            ->orderBy('created_at', 'desc')
            ->first();
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

        // Create approval decision
        $decision = $latestSubmission->approvalDecision()->create([
            'approver_id' => $user->id,
            'decision' => 'approved',
            'note' => null,
        ]);

        // Update task status
        $this->task->update(['status' => 'aprobado']);

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
            ],
            'reason' => 'Aprobación de entregable para la tarea: ' . $this->task->title,
            'ip_address' => request()->ip(),
        ]);

        $this->task->refresh();
        $this->task->load([
            'submissions.submitter',
            'submissions.deliverableVersion',
            'submissions.approvalDecision',
            'submissions.approvalDecision.approver',
        ]);

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

        // Create rejection decision
        $decision = $latestSubmission->approvalDecision()->create([
            'approver_id' => $user->id,
            'decision' => 'rejected',
            'note' => $this->reason,
        ]);

        // Update task status
        $this->task->update(['status' => 'rechazado']);

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
            ],
            'reason' => 'Rechazo de entregable para la tarea: ' . $this->task->title . ' — Razón: ' . $this->reason,
            'ip_address' => request()->ip(),
        ]);

        $this->showRejectModal = false;
        $this->reason = '';

        $this->task->refresh();
        $this->task->load([
            'submissions.submitter',
            'submissions.deliverableVersion',
            'submissions.approvalDecision',
            'submissions.approvalDecision.approver',
        ]);

        session()->flash('flash.banner', 'Entregable rechazado.');
    }

    public function render()
    {
        $latestSubmission = $this->latestSubmission;
        $approvalHistory = $this->task->submissions()
            ->with(['submitter', 'deliverableVersion', 'approvalDecision.approver'])
            ->whereHas('approvalDecision')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.tasks.approval-panel', [
            'latestSubmission' => $latestSubmission,
            'approvalHistory' => $approvalHistory,
            'canApprove' => $this->canApprove(),
        ]);
    }
}
