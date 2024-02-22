<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    use HasFactory;
    protected $guarded = [];

    //An admin belongs to a profile
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
