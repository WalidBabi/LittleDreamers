<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Parentt extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'parents';
    // a parent belongs to a profile
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
    // a parent has many children
    public function children(): HasMany
    {
        return $this->hasMany(Child::class ,'parent_id');
    }
}
