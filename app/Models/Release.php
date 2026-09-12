<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Release extends Model
{
    protected $fillable = [
        'version', 'channel', 'changelog', 'zip_path', 'sha256', 'signature', 'published_at', 'published_by',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
