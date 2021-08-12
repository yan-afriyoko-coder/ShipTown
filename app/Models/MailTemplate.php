<?php

namespace App\Models;

use App\BaseModel;

class MailTemplate extends BaseModel
{
    protected $fillable = ['subject', 'html_template', 'text_template', 'reply_to'];

    public function getNameAttribute()
    {
        return preg_replace('/(?<!\ )[A-Z]/', ' $0', class_basename($this->mailable));
    }
}
