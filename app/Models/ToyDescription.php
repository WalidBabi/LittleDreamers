<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ToyDescription extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'toys_descriptions';
    // a toy_description belongs to a toy
    public function toy(): HasOne
    {
        return $this->hasOne(Toy::class);
    }
    // a toy belongs to a company
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class,'company_id');
    }
}
