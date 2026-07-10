<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_folder_id',
        'name',
        'description',
        'created_by',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(DocumentFolder::class, 'document_folder_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentFileVersion::class);
    }

    public function latestVersion()
    {
        return $this->hasOne(DocumentFileVersion::class)->latestOfMany('version_number');
    }
}
