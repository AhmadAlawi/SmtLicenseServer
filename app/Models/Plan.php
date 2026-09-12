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

    /** "334 SAR" style label for a currency — reads the minor-unit amount from currency_prices (USD included, same shape). Falls back to display_price when that currency has no amount configured. */
    public function formattedPriceFor(string $currency): ?string
    {
        $currency = strtoupper($currency);
        $amount   = $this->currency_prices[$currency]['amount'] ?? null;
        if ($amount === null) {
            return $this->display_price;
        }

        $major = rtrim(rtrim(number_format($amount / 100, 2, '.', ''), '0'), '.');

        return $currency === 'USD' ? "\${$major}" : "{$major} {$currency}";
    }

    /** @return array<string,string> currency code => formatted label, for the header/wizard dropdown's client-side price swap. */
    public function priceTable(): array
    {
        $table = ['USD' => $this->formattedPriceFor('USD')];
        foreach (self::CURRENCIES as $currency) {
            $table[$currency] = $this->formattedPriceFor($currency);
        }

        return array_filter($table);
    }
}
