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

    public function mount(Task $task)
    {
        $this->task = $task->load(['deliverableVersions', 'deliverableVersions.uploader', 'submissions', 'submissions.deliverableVersion', 'submissions.approvalDecision', 'submissions.approvalDecision.approver']);
    }

    public function submitDeliverable()
    {
        $this->validate();

        $user = Auth::user();

        // Determine next version number
        $latestVersion = $this->task->deliverableVersions()
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
        $deliverableVersion = $this->task->deliverableVersions()->create([
            'version_number' => $versionNumber,
            'original_filename' => $originalName,
            'storage_key' => $storedFile,
            'mime_type' => $mimeType,
            'size_bytes' => $sizeBytes,
            'checksum' => $checksum,
            'uploader_id' => $user->id,
            'upload_note' => $this->uploadNote ?: null,
        ]);

        // Create TaskSubmission
        $submission = $this->task->submissions()->create([
            'deliverable_version_id' => $deliverableVersion->id,
            'submitter_id' => $user->id,
            'notes' => $this->uploadNote ?: null,
        ]);

        // Update task status
        $this->task->update(['status' => 'entregado']);

        // Audit log
        AuditEvent::create([
            'user_id' => $user->id,
            'project_id' => $this->task->projectGroup->project_id,
            'task_id' => $this->task->id,
            'action' => 'deliverable_uploaded',
            'entity_type' => 'deliverable_version',
            'entity_id' => (string) $deliverableVersion->id,
            'after_data' => [
                'version_number' => $versionNumber,
                'filename' => $originalName,
                'size_bytes' => $sizeBytes,
            ],
            'reason' => 'Subida de entregable v' . $versionNumber . ' para la tarea: ' . $this->task->title,
            'ip_address' => request()->ip(),
        ]);

        // Reset form
        $this->file = null;
        $this->uploadNote = '';

        // Refresh task
        $this->task->refresh();
        $this->task->load(['deliverableVersions.uploader', 'submissions.deliverableVersion', 'submissions.approvalDecision.approver']);

        $this->dispatch('deliverable-uploaded');
        session()->flash('flash.banner', 'Entregable subido exitosamente.');
    }

    public function render()
    {
        $versions = $this->task->deliverableVersions()
            ->with('uploader')
            ->orderBy('version_number', 'desc')
            ->get();

        $submissions = $this->task->submissions()
            ->with(['deliverableVersion', 'approvalDecision.approver'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.tasks.deliverable-upload', [
            'versions' => $versions,
            'submissions' => $submissions,
        ]);
    }
}
