<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_group_id',
        'title',
        'description',
        'order',
        'duration_days',
        'is_required',
        'is_deliverable',
    ];

    protected $casts = [
        'order' => 'integer',
        'duration_days' => 'integer',
        'is_required' => 'boolean',
        'is_deliverable' => 'boolean',
    ];

    public function templateGroup(): BelongsTo
    {
        return $this->belongsTo(TemplateGroup::class);
    }

    public function templateSubtasks(): HasMany
    {
        return $this->hasMany(TemplateSubtask::class);
    }

    public function responsibles(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'template_task_responsibles');
    }

    public function approvers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'template_task_approvers');
    }
}
