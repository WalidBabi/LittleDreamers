<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Profile extends Authenticatable
{
    use HasApiTokens, HasFactory;
    protected $guarded = [];
    // a profile has many admins
    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }
// a profile has many parents
    public function parents(): HasMany
    {
        return $this->hasMany(Parent::class);
    }
}