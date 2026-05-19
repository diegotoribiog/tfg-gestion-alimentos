<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

use Illuminate\Auth\Notifications\ResetPassword;
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
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Personalización del correo de restablecimiento de contraseña
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Restablecer contraseña - midespensa')
                ->greeting('¡Hola!')
                ->line('Recibiste este correo porque solicitaste restablecer la contraseña de tu cuenta en midespensa.')
                ->action('Restablecer Contraseña', $url)
                ->line('Este enlace para restablecer la contraseña caducará en ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire') . ' minutos.')
                ->line('Si no realizaste esta solicitud, no es necesario realizar ninguna otra acción.')
                ->salutation('Saludos, el equipo de midespensa.');
        });
    }
}
