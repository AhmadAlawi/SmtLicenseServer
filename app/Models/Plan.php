<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['code', 'name', 'display_price', 'seat_limit', 'features', 'stripe_price_id', 'currency_prices', 'is_active'];

    /** Supported currencies for the header dropdown, in display order. USD is always available via stripe_price_id, not this list. */
    public const CURRENCIES = ['JOD', 'SAR', 'AED', 'EGP'];

    protected function casts(): array
    {
        return [
            'features'        => 'array',
            'currency_prices' => 'array',
            'is_active'       => 'boolean',
        ];
    }

    /** The Stripe Price ID to charge for a given currency code — falls back to the USD default when that currency has no price set up yet. */
    public function stripePriceIdFor(string $currency): string
    {
        $currency = strtoupper($currency);
        if ($currency === 'USD') {
            return (string) $this->stripe_price_id;
        }

        return $this->currency_prices[$currency]['stripe_price_id'] ?? (string) $this->stripe_price_id;
    }
}
