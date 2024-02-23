<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Child extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'children';
    //a child has many orders
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    //a child belongs to a parent
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Parentt::class);
    }
    //a child has many reviews on many toys
    public function toys(): BelongsToMany
    {
        return $this->belongsToMany(Toy::class, 'review' ,'toy_id' ,'child_id')->withTimestamps();
    }
}
