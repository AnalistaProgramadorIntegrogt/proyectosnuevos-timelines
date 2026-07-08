<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliverableVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'version_number',
        'original_filename',
        'storage_key',
        'mime_type',
        'size_bytes',
        'checksum',
        'uploader_id',
        'upload_note',
    ];

    protected $casts = [
        'version_number' => 'integer',
        'size_bytes' => 'integer',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }
}
