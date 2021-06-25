<?php

namespace App\Mail;

use App\Models\UserInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteCreated extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $invite;

    /**
     * Create a new message instance.
     *
     * @param UserInvite $invite
     */
    public function __construct(UserInvite $invite)
    {
        $this->invite = $invite;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.invite', ['invite' => $this->invite]);
    }
}
