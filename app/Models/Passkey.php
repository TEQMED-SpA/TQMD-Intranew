<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Passkey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'credential_id',
        'public_key',
        'counter',
        'transports',
        'attestation_type',
        'backed_up',
        'device_type',
        'last_used_at',
    ];

    protected $casts = [
        'transports' => 'array',
        'backed_up' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
