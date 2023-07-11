<?php

namespace App\Modules\Slack\src\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingWebhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'webhook_url',
    ];
}
