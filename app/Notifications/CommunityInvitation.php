<?php

namespace App\Notifications;

use App\Models\CommunityInvitation as InvitationModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommunityInvitation extends Notification
{
    use Queueable;

    public $invitation;
    public $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(InvitationModel $invitation, $url)
    {
        $this->invitation = $invitation;
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Recibiste una invitación a participar en la comunidad ' . $this->invitation->community->name . '.')
            ->action('Aceptar invitación', url($this->url))
            ->line('Gracias por utilizar CompanyHike!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'invitation_id' => $this->invitation->id,
            'community_name' => $this->invitation->community->name,
            'action' => $this->url,
        ];
    }
}
