<?php

namespace App\Mail;

use App\Models\MailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Spatie\MailTemplates\TemplateMailable;

class OrderMail extends TemplateMailable
{
    use Queueable, SerializesModels;

    private $template;

    public array $variables;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(MailTemplate $template, array $variables = [])
    {
        $this->template = $template;
        $this->variables = $variables;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        if ($this->template->reply_to) {
            $this->replyTo($this->template->reply_to);
        }

        return $this;
    }
}
