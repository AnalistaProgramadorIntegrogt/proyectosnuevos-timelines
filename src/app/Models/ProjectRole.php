<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'can_manage_settings',
        'can_manage_roles',
        'can_add_edit_tasks',
        'can_reorder_tasks',
        'can_upload_deliverables',
        'can_approve_tasks',
        'can_edit_status',
        'can_view_audit',
    ];

    protected $casts = [
        'can_manage_settings' => 'boolean',
        'can_manage_roles' => 'boolean',
        'can_add_edit_tasks' => 'boolean',
        'can_reorder_tasks' => 'boolean',
        'can_upload_deliverables' => 'boolean',
        'can_approve_tasks' => 'boolean',
        'can_edit_status' => 'boolean',
        'can_view_audit' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function visibilityRules(): HasMany
    {
        return $this->hasMany(RoleVisibilityRule::class);
    }
}
