<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Toy extends Model
{
    use HasFactory;
    protected $guarded = [];
    //a toy has one description
    public function toy_description(): HasOne
    {
        return $this->hasOne(ToyDescription::class);
    }
    // a toy belongs to a company
    public function company(): belongsTo
    {
        return $this->belongsTo(Company::class);
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
