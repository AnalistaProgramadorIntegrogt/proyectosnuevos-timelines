<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class TemplateGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_template_version_id',
        'name',
        'order',
        'is_gate',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_gate' => 'boolean',
    ];

    public function processTemplateVersion(): BelongsTo
    {
        return $this->belongsTo(ProcessTemplateVersion::class);
    }

    public function templateTasks(): HasMany
    {
        return $this->hasMany(TemplateTask::class);
    }

    public function templateSubtasks(): HasManyThrough
    {
        return $this->hasManyThrough(TemplateSubtask::class, TemplateTask::class);
    }
}
