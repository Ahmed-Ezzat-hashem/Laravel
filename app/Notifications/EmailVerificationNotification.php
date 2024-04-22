<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Otp;

class EmailVerificationNotification extends Notification
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
        //$this->message = 'Use the below code for reset your password';
        $this->subject = 'Email Verification';
        $this->message = 'Use the following code to verify your email address: ';
        $this->fromEmail = "test@ahmedshaltout.com";
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
        $otp = $this->otp->generate($notifiable->email, 6, 2);

        return (new MailMessage)
            ->view('emails.custom_email', ['user' => $notifiable, 'otp' => $otp->token , 'message' => $this->message])
            ->mailer('smtp')
            ->subject($this->subject)
            ->greeting('Hello ' . $notifiable->user_name)
            ->line($otp->token)
            ->salutation(' ');
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
