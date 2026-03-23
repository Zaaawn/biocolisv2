<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StripeWebhook extends Model
{
    use HasFactory;

    protected $table = 'stripe_webhooks';

    protected $fillable = [
        'stripe_event_id', 'type', 'payload', 'statut', 'erreur', 'traite_at',
    ];

    protected function casts(): array
    {
        return [
            'payload'    => 'array',
            'traite_at'  => 'datetime',
        ];
    }
}
