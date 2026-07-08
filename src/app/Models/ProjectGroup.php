<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProjectGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'order',
        'status',
        'is_gate',
        'gate_decision_role',
        'unlocks_group_id',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_gate' => 'boolean',
        'status' => 'string',
        'gate_decision_role' => 'string',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function unlocksGroup(): BelongsTo
    {
        return $this->belongsTo(ProjectGroup::class, 'unlocks_group_id');
    }

    public function gateDecision(): HasOne
    {
        return $this->hasOne(GateDecision::class);
    }
}
