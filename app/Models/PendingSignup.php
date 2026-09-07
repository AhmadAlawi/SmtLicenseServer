<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PendingSignup extends Model
{
    protected $fillable = [
        'token', 'plan_code', 'subdomain_slug', 'company_name',
        'admin_name', 'admin_email', 'admin_password',
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
