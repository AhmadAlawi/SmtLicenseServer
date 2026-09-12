<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialConnection extends Model
{
    protected $fillable = [
        'provider', 'user_access_token', 'ad_account_id', 'ad_account_name',
        'page_id', 'page_name', 'instagram_business_account_id', 'token_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'user_access_token' => 'encrypted',
            'token_expires_at'  => 'datetime',
        ];
    }
}
