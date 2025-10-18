<?php

namespace App\Providers;

use App\Services\SocialAuthService;
use App\Services\AppleService;
use App\Services\FacebookService;
use App\Services\GoogleService;
use Illuminate\Support\ServiceProvider;

class SocialAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SocialAuthService::class, function ($app) {
            return new SocialAuthService([
                'google'   => new GoogleService(),
                'facebook' => new FacebookService(),
                'apple'    => new AppleService(),
            ]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
