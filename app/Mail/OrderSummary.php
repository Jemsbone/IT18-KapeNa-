<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderSummary extends Mailable
{
    use Queueable, SerializesModels;

    public $cartData;
    public $orderSummary;
    public $user;
    public $order;
    public $logoDataUri;

    /**
     * Create a new message instance.
     */
    public function __construct($cartData, $orderSummary, $user, $order)
    {
        $this->cartData = $cartData;
        $this->orderSummary = $orderSummary;
        $this->user = $user;
        $this->order = $order;
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
        return $this->subject('Kape Na! - Order Confirmation #' . $this->order->order_number)
                    ->view('emails.order-summary')
                    ->with([
                        'cartData' => $this->cartData,
                        'orderSummary' => $this->orderSummary,
                        'user' => $this->user,
                        'order' => $this->order,
                        'logoDataUri' => $this->logoDataUri,
                    ]);
    }
}
