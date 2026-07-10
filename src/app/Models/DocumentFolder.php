<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'description',
        'created_by',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DocumentFolder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(DocumentFolder::class, 'parent_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(DocumentFile::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
