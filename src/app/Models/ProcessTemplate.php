<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProcessTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function versions(): HasMany
    {
        return $this->hasMany(ProcessTemplateVersion::class);
    }

    public function latestPublishedVersion(): HasOne
    {
        return $this->hasOne(ProcessTemplateVersion::class)
            ->where('status', 'published')
            ->latest('version_number');
    }
}
