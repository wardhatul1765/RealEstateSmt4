<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString; // Untuk menggunakan HTML di email

class SendPasswordResetCode extends Notification implements ShouldQueue // Implementasi ShouldQueue agar pengiriman email tidak menghambat response
{
    use Queueable;

    public string $code;
    public string $userName;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $code, string $userName)
    {
        $this->code = $code;
        $this->userName = $userName;
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
        $appName = config('app.name', 'Nestora App'); // Ambil nama aplikasi dari config
        $expirationMinutes = 10; // Sesuaikan dengan logika di controller

        return (new MailMessage)
                    ->subject("[$appName] Kode Reset Password Anda")
                    ->greeting("Halo {$this->userName},")
                    ->line("Kami menerima permintaan untuk mereset password akun Anda di {$appName}.")
                    ->line(new HtmlString("Gunakan kode berikut untuk mereset password Anda: <br><br><strong style='font-size: 24px; letter-spacing: 2px; color: #1D2B36; padding: 10px 15px; background-color: #f0f0f0; border-radius: 5px;'>{$this->code}</strong>"))
                    ->line("Kode ini hanya berlaku selama {$expirationMinutes} menit.")
                    ->line("Jika Anda tidak meminta reset password, abaikan email ini. Keamanan akun Anda tetap terjaga.")
                    ->salutation(new HtmlString("Salam hormat,<br>Tim {$appName}"));
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