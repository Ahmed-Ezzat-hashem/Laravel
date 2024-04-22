<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;

class VonageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            $credentials = new Basic(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));
            return new Client($credentials);
        });
    }
}

