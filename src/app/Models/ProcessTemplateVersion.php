<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcessTemplateVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_template_id',
        'version_number',
        'template_data',
        'status',
        'notes',
    ];

    protected $casts = [
        'version_number' => 'integer',
        'template_data' => 'array',
        'status' => 'string',
    ];

    public function processTemplate(): BelongsTo
    {
        return $this->belongsTo(ProcessTemplate::class);
    }

    public function templateGroups(): HasMany
    {
        return $this->hasMany(TemplateGroup::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
