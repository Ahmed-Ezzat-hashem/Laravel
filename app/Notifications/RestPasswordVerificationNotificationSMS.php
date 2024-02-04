<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RestPasswordVerificationNotificationSMS extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->message = 'Use the below code for rest your password';
        $this->subject = 'Password Restting' ;
        $this->fromEmail = "test@ahmedshaltout.com" ;
        $this->mailer = 'smtp';
        $this->otp = new Otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['nexmo'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toNexmo(object $notifiable): MailMessage
    {
        $otp = $this->otp->generate($notifiable->phone,6, 60);
        return (new NexmoMessage())
        ->content("Your OTP for password reset is: " . $otp->token);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
