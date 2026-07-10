<?php

namespace App\Http\Livewire\Tasks;

use App\Models\AuditEvent;
use App\Models\DeliverableVersion;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class DeliverableUpload extends Component
{
    use WithFileUploads;

    public Task $task;
    public ?\App\Models\Subtask $subtask = null;

    /** @var \Livewire\TemporaryUploadedFile|null */
    public $file;

    public $uploadNote = '';

    protected $rules = [
        'file' => 'required|file|max:102400', // 100 MB max
        'uploadNote' => 'nullable|string|max:2000',
    ];

    protected $messages = [
        'file.required' => 'Debes seleccionar un archivo para subir.',
        'file.max' => 'El archivo no puede superar los 100 MB.',
        'uploadNote.max' => 'La nota no puede exceder los 2000 caracteres.',
    ];

    public function mount(Task $task, $subtask = null)
    {
        $this->task = $task;
        if ($subtask) {
            $this->subtask = $subtask->load(['deliverableVersions', 'deliverableVersions.uploader', 'task.projectGroup']);
        } else {
            $this->task->load(['deliverableVersions', 'deliverableVersions.uploader', 'submissions', 'submissions.deliverableVersion', 'submissions.approvalDecision', 'submissions.approvalDecision.approver']);
        }
    }

    protected function getTarget()
    {
        return $this->subtask ?? $this->task;
    }

    public function submitDeliverable()
    {
        $this->validate();

        $user = Auth::user();
        $target = $this->getTarget();

        // Determine next version number
        $latestVersion = $target->deliverableVersions()
            ->orderBy('version_number', 'desc')
            ->first();

        $versionNumber = $latestVersion ? $latestVersion->version_number + 1 : 1;

        // Store the file
        $storedFile = $this->file->store('deliverables', 'local');
        $originalName = $this->file->getClientOriginalName();
        $mimeType = $this->file->getMimeType();
        $sizeBytes = $this->file->getSize();
        $checksum = md5_file($this->file->getRealPath());

        // Create DeliverableVersion
        $deliverableVersion = $target->deliverableVersions()->create([
            'version_number' => $versionNumber,
            'original_filename' => $originalName,
            'storage_key' => $storedFile,
            'mime_type' => $mimeType,
            'size_bytes' => $sizeBytes,
            'checksum' => $checksum,
            'uploader_id' => $user->id,
            'upload_note' => $this->uploadNote ?: null,
        ]);

        // If it's a subtask, we also need to create a TaskSubmission?
        // Wait, yes, subtasks also have TaskSubmissions now!
        if ($this->subtask) {
            $submission = $this->subtask->task->submissions()->create([
                'subtask_id' => $this->subtask->id,
                'deliverable_version_id' => $deliverableVersion->id,
                'submitter_id' => $user->id,
                'notes' => $this->uploadNote ?: null,
            ]);
        } else {
            $submission = $this->task->submissions()->create([
                'deliverable_version_id' => $deliverableVersion->id,
                'submitter_id' => $user->id,
                'notes' => $this->uploadNote ?: null,
            ]);
        }

        // Update target status
        $target->update(['status' => 'entregado']);

        // Audit log
        $projectId = $this->task->projectGroup ? $this->task->projectGroup->project_id : null;
        
        AuditEvent::create([
            'user_id' => $user->id,
            'project_id' => $projectId,
            'task_id' => $this->task->id,
            'action' => 'deliverable_uploaded',
            'entity_type' => 'deliverable_version',
            'entity_id' => (string) $deliverableVersion->id,
            'after_data' => [
                'version_number' => $versionNumber,
                'filename' => $originalName,
                'size_bytes' => $sizeBytes,
                'subtask_id' => $this->subtask ? $this->subtask->id : null,
            ],
            'reason' => 'Subida de entregable v' . $versionNumber . ' para ' . ($this->subtask ? 'subtarea: ' . $this->subtask->title : 'la tarea: ' . $this->task->title),
            'ip_address' => request()->ip(),
        ]);

        // Reset form
        $this->file = null;
        $this->uploadNote = '';

        // Refresh target
        $target->refresh();
        if ($this->subtask) {
            // TaskSubmissions are technically related to the subtask now (with subtask_id)
        } else {
            $this->task->load(['deliverableVersions.uploader', 'submissions.deliverableVersion', 'submissions.approvalDecision.approver']);
        }

        $this->dispatch('deliverable-uploaded');
        session()->flash('flash.banner', 'Entregable subido exitosamente.');
    }

    public function render()
    {
        $target = $this->getTarget();

        $versions = $target->deliverableVersions()
            ->with('uploader')
            ->orderBy('version_number', 'desc')
            ->get();

        // For subtasks, get submissions by subtask_id. For tasks, get by task_id where subtask_id is null?
        // Let's query submissions
        if ($this->subtask) {
            $submissions = TaskSubmission::where('subtask_id', $this->subtask->id)
                ->with(['deliverableVersion', 'approvalDecision.approver'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $submissions = TaskSubmission::where('task_id', $this->task->id)
                ->whereNull('subtask_id')
                ->with(['deliverableVersion', 'approvalDecision.approver'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('livewire.tasks.deliverable-upload', [
            'versions' => $versions,
            'submissions' => $submissions,
            'isSubtask' => $this->subtask !== null,
        ]);
    }
}
