<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_group_id',
        'title',
        'description',
        'order',
        'duration_days',
        'calculated_start_date',
        'calculated_end_date',
        'baseline_end_date',
        'status',
        'is_required',
        'is_deliverable',
        'responsible_user_id',
        'explicit_approver_id',
        'real_end_date',
    ];

    protected $casts = [
        'order' => 'integer',
        'duration_days' => 'integer',
        'calculated_start_date' => 'date',
        'calculated_end_date' => 'date',
        'baseline_end_date' => 'date',
        'real_end_date' => 'date',
        'status' => 'string',
        'is_required' => 'boolean',
        'is_deliverable' => 'boolean',
    ];

    public function projectGroup(): BelongsTo
    {
        return $this->belongsTo(ProjectGroup::class);
    }

    public function responsibles(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_responsibles');
    }

    public function approvers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_approvers');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class, 'task_id');
    }

    public function deliverableVersions(): HasMany
    {
        return $this->hasMany(DeliverableVersion::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function auditEvents(): MorphMany
    {
        return $this->morphMany(AuditEvent::class, 'auditable');
    }
}
