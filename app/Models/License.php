<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * One row per provisioned POS instance. `license_key_hash` is the only
 * form of the key stored (see the migration) — lookups always hash the
 * incoming X-License-Key header before querying. `hmac_secret` is stored
 * encrypted (the `encrypted` cast) and only ever compared, never returned
 * by any API response after the one-time /register call.
 */
class License extends Model
{
    protected $fillable = [
        'customer_id', 'plan_id', 'license_key_hash', 'hmac_secret',
        'instance_domain', 'fingerprint', 'status', 'grace_until', 'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'hmac_secret'  => 'encrypted',
            'grace_until'  => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function instance(): HasOne
    {
        return $this->hasOne(Instance::class);
    }

    public function usageReports(): HasMany
    {
        return $this->hasMany(UsageReport::class);
    }

    public static function hashKey(string $licenseKey): string
    {
        return hash('sha256', $licenseKey);
    }
}
