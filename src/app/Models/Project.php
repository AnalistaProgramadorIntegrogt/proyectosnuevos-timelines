<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'owner_id',
        'start_date',
        'default_approver_id',
        'lifecycle_status',
        'outcome',
        'archived',
        'process_template_version_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'archived' => 'boolean',
        'lifecycle_status' => 'string',
        'outcome' => 'string',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function defaultApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'default_approver_id');
    }

    public function processTemplateVersion(): BelongsTo
    {
        return $this->belongsTo(ProcessTemplateVersion::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(ProjectGroup::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(ProjectRole::class);
    }

    public function auditEvents(): HasMany
    {
        return $this->hasMany(AuditEvent::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
