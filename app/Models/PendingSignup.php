<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PendingSignup extends Model
{
    protected $fillable = [
        'token', 'plan_code', 'subdomain_slug', 'company_name', 'branch_count', 'logo_path',
        'admin_name', 'admin_email', 'admin_password',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'landing_page',
    ];

    protected function casts(): array
    {
        return ['admin_password' => 'encrypted'];
    }

    public static function makeToken(): string
    {
        return Str::random(48);
    }
}
