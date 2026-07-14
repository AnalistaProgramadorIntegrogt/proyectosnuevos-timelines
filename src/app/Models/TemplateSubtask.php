<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TemplateSubtask extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_task_id',
        'title',
        'description',
        'duration_days',
        'is_deliverable',
        'order',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'order' => 'integer',
        'is_deliverable' => 'boolean',
    ];

    public function templateTask(): BelongsTo
    {
        return $this->belongsTo(TemplateTask::class);
    }

    public function responsibles(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'template_subtask_responsibles');
    }

    public function approvers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'template_subtask_approvers');
    }
}
