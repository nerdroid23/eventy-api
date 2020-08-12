<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeToEventy extends Notification implements ShouldQueue
{
    use Queueable;

    /*** @var \App\User */
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param \App\User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(env('ALLOWED_ORIGIN') . '/login');

        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name') . '!')
            ->greeting('Hey, ' . $this->user->name . '!')
            ->line('We at ' . config('app.name') . ' are very excited to have you onboard!')
            ->line('We hope that you\'ll be able to set up killer events using our platform.')
            ->action('Log in to your account', $url)
            ->line('You are receiving this email because an account was created using this email.')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
