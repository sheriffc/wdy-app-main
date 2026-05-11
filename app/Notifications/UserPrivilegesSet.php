<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserPrivilegesSet extends Notification
{
    use Queueable;

    public $details;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
//        logger("send email");
//        logger($notifiable->name);
//        logger($notifiable->email);

        return (new MailMessage)
                    ->subject('Your Application to '.config('app.name').' Has Been Reviewed')
                    ->greeting('Hi '.$notifiable->name.',')
                    ->line('An Administrator ('.$this->details['admin_name'].') has reviewed your application and granted you access as: '.$this->details['user_type_name'])
                    ->line('You may now access the website')
                    ->action('Go to website to login', url('/login'))
        ;
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
            //
        ];
    }
}
