<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['code', 'name', 'display_price', 'seat_limit', 'features', 'stripe_price_id', 'is_active'];

    protected function casts(): array
    {
        return [
            'features'  => 'array',
            'is_active' => 'boolean',
        ];
    }
}
