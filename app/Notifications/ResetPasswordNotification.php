<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject(Lang::get('TEQMED SpA - Restablecer contraseña'))
            ->greeting(Lang::get('¡Hola!'))
            ->line(Lang::get('Está recibiendo este correo electrónico porque recibimos una solicitud de restablecimiento de contraseña para su cuenta.'))
            ->action(Lang::get('Restablecer contraseña'), $url)
            ->line(Lang::get('Este enlace de restablecimiento de contraseña caducará en :count minutos.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')]))
            ->line(Lang::get('Si no solicitó un restablecimiento de contraseña, no se requiere ninguna acción adicional.'))
            ->salutation(Lang::get('Saludos,') . "\n" . 'Equipo TEQMED SpA');
    }
}