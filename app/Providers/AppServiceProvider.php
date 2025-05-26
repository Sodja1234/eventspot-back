<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
Use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // public function boot(): void
    // {
    //     Schema::defaultStringLength(191);
    //     ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
    //         return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
    //     });
    // }

public function boot(): void
{
    // Personnalisation de l'URL envoyée par mail
    ResetPassword::createUrlUsing(function ($notifiable, string $token) {
        $frontendUrl = config('app.frontend_url', 'http://localhost:4200');
        $email = urlencode($notifiable->getEmailForPasswordReset());

        return "{$frontendUrl}/reset-password?token={$token}&email={$email}";
    });
}

}