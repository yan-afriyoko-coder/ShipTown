<?php

namespace App\Models;

use App\BaseModel;
use Barryvdh\LaravelIdeHelper\Eloquent;

/**
 * @method static where(string $string, string $get_class)
 * @mixin Eloquent
 */
class MailTemplate extends BaseModel
{
    protected $fillable = ['mailable', 'subject', 'html_template', 'text_template', 'reply_to', 'to'];

    public function getNameAttribute()
    {
        return preg_replace('/(?<!\ )[A-Z]/', ' $0', class_basename($this->mailable));
    }
}
