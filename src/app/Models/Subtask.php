<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subtask extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'title',
        'description',
        'duration_days',
        'start_date',
        'end_date',
        'status',
        'is_deliverable',
        'responsible_user_id',
        'explicit_approver_id',
        'order',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'string',
        'order' => 'integer',
        'is_deliverable' => 'boolean',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function deliverableVersions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DeliverableVersion::class, 'subtask_id');
    }

    public function responsibles(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subtask_responsibles');
    }

    public function approvers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subtask_approvers');
    }
}
