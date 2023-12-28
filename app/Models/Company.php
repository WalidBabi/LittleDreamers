<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $guarded = [];
    // a compant has many toys
    public function toys(): HasMany
    {
        return $this->hasMany(Toy::class);
    }
}
