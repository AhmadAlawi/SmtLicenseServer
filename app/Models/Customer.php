<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;

/** A paying POS customer (shop owner). Billable via Stripe/Cashier; may hold multiple licenses (e.g. one per branch instance). */
class Customer extends Model
{
    use Billable;

    protected $fillable = ['name', 'email'];

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }
}
