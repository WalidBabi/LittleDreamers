<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];
    // an order belongs to a child
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }
    // orders for many toys 
    public function toys(): BelongsToMany
    {
        return $this->belongsToMany(Toy::class, 'order_toy' ,'toy_id' ,'order_id')->withTimestamps();
    }
}
