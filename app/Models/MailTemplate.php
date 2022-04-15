<?php

namespace App\Models;

use App\BaseModel;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Spatie\MailTemplates\Interfaces\MailTemplateInterface;
use Spatie\MailTemplates\TemplateMailable;

/**
 * @mixin Eloquent
 * @property string code
 * @property string to
 * @property string reply_to
 * @property string html_template
 */
class MailTemplate extends BaseModel
{
    protected $fillable = [
        'code',
        'mailable',
        'subject',
        'html_template',
        'text_template',
        'reply_to',
        'to'
    ];

    public function getNameAttribute()
    {
        return $this->code;
    }
}
