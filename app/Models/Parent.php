<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parent extends Model
{
    use HasFactory;
    protected $guarded = [];
    // a parent belongs to a profile
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
    // a parent has many children
    public function children(): HasMany
    {
        return $this->hasMany(Child::class);
    }
}
