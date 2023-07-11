<?php

namespace App\Modules\Slack\src\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $webhook_url
 */
class IncomingWebhook extends Model
{
    use HasFactory;

    protected $table = 'modules_slack_incoming_webhooks';

    protected $fillable = [
        'webhook_url',
    ];
}
