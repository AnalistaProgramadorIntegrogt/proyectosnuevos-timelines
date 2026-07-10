<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'subtask_id',
        'deliverable_version_id',
        'submitter_id',
        'notes',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function subtask(): BelongsTo
    {
        return $this->belongsTo(Subtask::class);
    }

    public function deliverableVersion(): BelongsTo
    {
        return $this->belongsTo(DeliverableVersion::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitter_id');
    }

    public function approvalDecision(): HasOne
    {
        return $this->hasOne(ApprovalDecision::class);
    }
}
