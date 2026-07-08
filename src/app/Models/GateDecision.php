<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GateDecision extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_group_id',
        'decision_maker_id',
        'outcome',
        'notes',
    ];

    protected $casts = [
        'outcome' => 'string',
    ];

    public function projectGroup(): BelongsTo
    {
        return $this->belongsTo(ProjectGroup::class);
    }

    public function decisionMaker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decision_maker_id');
    }
}
