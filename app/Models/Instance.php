<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Instance extends Model
{
    protected $fillable = [
        'license_id', 'db_credentials_ref', 'container_ref', 'subdomain',
        'railway_service_id', 'railway_db_service_id',
        'subdomain_slug', 'default_domain', 'custom_domain', 'custom_domain_dns_target',
        'provisioning_status', 'provisioning_error', 'provisioning_check_attempts', 'last_seen_at',
    ];

    protected function casts(): array
    {
        return ['last_seen_at' => 'datetime'];
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }
}
