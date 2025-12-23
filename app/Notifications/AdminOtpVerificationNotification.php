<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class AdminOtpVerificationNotification extends Notification
{
    use Queueable;

    /**
     * The OTP code to send
     */
    public string $otpCode;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $otpCode)
    {
        $this->otpCode = $otpCode;
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
        // Try to find and embed the logo as base64 for email compatibility
        $logoDataUri = null;
        $logoPath = null;
        
        // Check possible logo locations (in order of preference)
        $possiblePaths = [
            public_path('images/kape-na-logo.png'),
            public_path('images/kape-na-logo.jpg'),
            public_path('images/kape-na-logo.webp'),
            public_path('images/logo.png'),
            public_path('images/logo.jpg'),
            public_path('images/logo.webp'),
            public_path('images/logo-removebg-preview.png'),
            public_path('images/CoffeeImage/logo.png'),
            public_path('images/CoffeeImage/logo.jpg'),
            public_path('images/CoffeeImage/logo.webp'),
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $logoPath = $path;
                break;
            }
        }
        
        // Convert logo to base64 data URI if found
        if ($logoPath) {
            $imageData = file_get_contents($logoPath);
            $imageInfo = getimagesize($logoPath);
            $mimeType = $imageInfo['mime'] ?? 'image/png';
            $logoDataUri = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
        }

        return (new MailMessage)
            ->subject('Verify Your Admin Email - Kape Na!')
            ->view('emails.otp-verification', [
                'user' => $notifiable,
                'otpCode' => $this->otpCode,
                'logoDataUri' => $logoDataUri,
            ]);
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
