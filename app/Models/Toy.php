<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Toy extends Model
{
    use HasFactory;
    protected $guarded = [];
    //a toy has one description
    public function toy_description(): BelongsTo
    {
        return $this->belongsTo(ToyDescription::class, 'toy_description_id');
    }
    // a toy will be in many orders
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class);
    }
    // a toy will be reviewed by many children
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Child::class);
    }
}
