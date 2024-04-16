<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Illuminate\Support\Facades\Config;
use Otp;

class ResetPasswordVerificationNotificationSMS extends Notification
{
    protected $otpService;
    protected $userPhone;
    protected $otp;

    public function __construct($userPhone)
    {
        $this->otpService = new Otp;
        $this->userPhone = $userPhone;
    }

    public function via($notifiable)
    {
        return ['vonage'];
    }

    public function toVonage($notifiable)
    {
        // Generate an OTP for the user
        $this->otp = $this->otpService->generate($this->userPhone);

        // Set up the Vonage client
        $credentials = new Basic(Config::get('services.vonage.api_key'), Config::get('services.vonage.api_secret'));
        $vonage = new Client($credentials);

        // Compose the SMS message
        $message = "Your OTP code is: " . $this->otp->token;

        // Send the SMS using Vonage
        $response = $vonage->sms()->send(
            new \Vonage\SMS\Message\SMS(
                '+2' . $notifiable->phone, // User's phone number with country code
                Config::get('services.vonage.from'), // Sender ID or Vonage number
                $message
            )
        );

        // Check the response for success or failure
        if (!$response->current()->isSuccess()) {
            // Handle error cases (log error, notify user, etc.)
            throw new \Exception("Failed to send SMS via Vonage: " . $response->current()->getErrorText());
        }
    }
}
