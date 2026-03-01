<?php

namespace App\Notifications\Auth;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class SendOTPNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(public string $otp) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Password Reset Verification Code')
            ->greeting('Hello!')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->line('Your 6-digit verification code is:')
            ->line(new HtmlString('<h1>'.$this->otp.'</h1>'))
            ->line('If you did not request a password reset, no further action is required.');
    }
}
