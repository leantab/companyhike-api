<?php

namespace App\Mail;

use App\Models\Community;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MatchInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;
    public $community;
    public $match;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Community $community, $url)
    {
        $this->community = $community;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from([config('app.mail_from_name') => config('app.mail_from_address')])
            ->markdown('emails.invitations.match', [
                'url' => $this->url,
                'community' => $this->community,
            ]);
    }
}
}
