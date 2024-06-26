<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Otp;
class ResetPasswordVerificationNotification extends Notification
{
    use Queueable;
    public $message;
    public $subject;
    public $fromEmail;
    public $mailer;
    private $otp;
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $otp = $this->otp->generate($notifiable->email,6, 2);
        return (new MailMessage)
        ->view('emails.password_email', ['user' => $notifiable, 'otp' => $otp->token , 'message' => $this->message])
        ->mailer( 'smtp' )
        ->subject($this->subject)
        ->greeting( 'Hello '.$notifiable->user_name)
        ->line($this->message)
        ->line('code :'. $otp->token);
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
