<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToyDescription extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'toys_descriptions';
    // a toy_description belongs to a toy
    public function toy(): BelongsTo
    {
        return $this->belongsTo(Toy::class);
    }
}
