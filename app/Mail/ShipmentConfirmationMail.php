<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Spatie\MailTemplates\TemplateMailable;

class ShipmentConfirmationMail extends TemplateMailable
{
    use Queueable, SerializesModels;

    public array $variables;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $variables = [])
    {
        $this->variables = $variables;
    }
}
