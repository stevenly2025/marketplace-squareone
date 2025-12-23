<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    // 1. Definisikan variabel token
    public $token;

    /**
     * Create a new notification instance.
     */
    // 2. Wajib ada parameter $token di sini agar "1 accepted"
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
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
        // URL Reset Password yang benar
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('🔒 Reset Password SquareOne Anda')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Kami menerima permintaan untuk mereset password akun SquareOne Anda.')
            ->action('Reset Password Sekarang', $url)
            ->line('Tautan ini akan kedaluwarsa dalam 60 menit.')
            ->line('Jika Anda tidak meminta reset password, abaikan email ini.')
            ->salutation('Salam aman, Tim SquareOne');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}