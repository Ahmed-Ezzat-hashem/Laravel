<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Vonage\Client;
use Vonage\SMS\Message\Text;
use Vonage\Client\Credentials\Basic;


class SmsController extends Controller
{
    protected $vonageClient;

    public function sendSms(Request $request)
    {
        // Define your API key and secret
        $apiKey = env('VONAGE_API_KEY');
        $apiSecret = env('VONAGE_API_SECRET');

        // Create credentials using the API key and secret
        $credentials = new Basic($apiKey, $apiSecret);

        // Create a Vonage client instance
        $client = new Client($credentials);

        // Define the sender, recipient, and message text
        $from = 'MediEye';
        $to = $user->phone;
        $messageText = 'Your message text';

        try {
            // Send an SMS message using the Vonage client
            $response = $client->sms()->send(
                new \Vonage\SMS\Message\SMS("+201112637733", 'MediEye', 'A text message sent using the Nexmo SMS API')
            );

            // Check the response for success
            if ($response->current()->getStatus() == 0) {
                // Message sent successfully
                return response()->json(['message' => 'SMS sent successfully!']);
            } else {
                // Error occurred while sending the message
                return response()->json(['message' => 'Failed to send SMS!'], 500);
            }
        } catch (\Exception $e) {
            // Handle any exceptions that may occur
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

}
