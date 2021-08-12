<?php

namespace App\Mail;

use App\Models\MailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Spatie\MailTemplates\TemplateMailable;

class OversoldProductMail extends TemplateMailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $variables = [])
    {
        $this->variables = $variables;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $template = MailTemplate::where('mailable', get_class($this))->first();

        if ($template->to) {
            $this->to($template->to);
        }

        return $this;
    }
}
