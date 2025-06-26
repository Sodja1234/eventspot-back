<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
Use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

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
    Carbon::setLocale('fr');
    date_default_timezone_set('Africa/Kinshasa');
    setlocale(LC_TIME, 'fr_FR.UTF-8');
    // Personnalisation de l'URL envoyée par mail
    Schema::defaultStringLength(191);
    ResetPassword::createUrlUsing(function ($notifiable, string $token) {
        $frontendUrl = config('app.frontend_url', 'http://localhost:4200');
        $email = urlencode($notifiable->getEmailForPasswordReset());

        return "{$frontendUrl}/reset-password?token={$token}&email={$email}";
    });

     // Email personnalisé de vérification avec lien + OTP
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            $otp = $notifiable->emailOtp()->latest()->first()?->otp ?? '******';

            return (new MailMessage)
                ->subject('Vérification de votre adresse e-mail')
                ->line('Bienvenue sur notre plateforme.')
                ->line(" Votre code OTP est : **$otp**")
                ->action('Vérifier mon email', $url)
                ->line('Ce lien expirera dans 60 minutes.')
                ->line('Si vous n’avez pas créé de compte, ignorez ce message.');
        });
    }
}