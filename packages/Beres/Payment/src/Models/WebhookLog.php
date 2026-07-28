<?php

namespace Beres\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Beres\Payment\Contracts\WebhookLog as WebhookLogContract;

class WebhookLog extends Model implements WebhookLogContract
{
    use HasFactory;

    protected $table = 'webhook_logs';

    protected $fillable = [
        'source',
        'payload',
        'headers',
        'processed',
        'error',
    ];

    protected $casts = [
        'payload'   => 'array',
        'headers'   => 'array',
        'processed' => 'boolean',
    ];
}
