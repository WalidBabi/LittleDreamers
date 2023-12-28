<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToyDescription extends Model
{
    use HasFactory;
    protected $guarded = [];
    // a toy_description belongs to a toy
    public function toy(): BelongsTo
    {
        return $this->belongsTo(Toy::class);
    }
}
