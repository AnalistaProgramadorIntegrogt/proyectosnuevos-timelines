<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function explicitApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'explicit_approver_id');
    }
}
