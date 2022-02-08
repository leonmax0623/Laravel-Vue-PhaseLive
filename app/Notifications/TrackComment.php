<?php

namespace App\Notifications;

use App\File;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrackComment extends Notification implements ShouldQueue
{
    use Queueable;

    public $triggerUser;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($triggerUser)
    {
        $this->triggerUser = $triggerUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $vias = ['database', 'broadcast'];

        if ($notifiable->notification_setting->activity_comment_on_mine_email) {
            $vias = array_merge(['mail'], $vias);
        }

        return $vias;
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
            "title" => 'New Comment on Track!',
            "message" => $this->triggerUser->name.' commented on your track',
            "image" => null
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("New Comment on Track")
            ->markdown('emails.notifications.comment', [
                'type' => 'Track',
                'user' => $notifiable,
                'message_user' => $this->triggerUser->name
            ]);
    }
}
