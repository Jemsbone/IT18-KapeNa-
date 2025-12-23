<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderReadyToPickup extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $user;
    public $logoDataUri;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->user = $order->user;
        $this->logoDataUri = $this->getLogoDataUri();
    }

    /**
     * Get logo as base64 data URI for email compatibility
     */
    private function getLogoDataUri()
    {
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
                $imageData = file_get_contents($path);
                $imageInfo = getimagesize($path);
                $mimeType = $imageInfo['mime'] ?? 'image/png';
                return 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
            }
        }
        
        return null;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Kape Na! - Your Order is Ready to Pickup #' . $this->order->order_number)
                    ->view('emails.order-ready-to-pickup')
                    ->with([
                        'order' => $this->order,
                        'user' => $this->user,
                        'logoDataUri' => $this->logoDataUri,
                    ]);
    }
}

