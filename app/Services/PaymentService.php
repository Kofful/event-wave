<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Carbon;

class PaymentService
{
    private string $privateKey;
    private string $publicKey;
    private string $environment;

    public function __construct()
    {
        $this->privateKey = config('payments.liqpay.private_key');
        $this->publicKey = config('payments.liqpay.public_key');
        $this->environment = config('app.env');
    }

    public function getData(Order $order): string
    {
        $ticket = $order->ticket;
        $event = $ticket->event;
        $eventDate = (new Carbon($event->date))->format('Y-m-d H:i');

        $data = [
            'version' => 3,
            'public_key' => $this->publicKey,
            'action' => 'pay',
            'amount' => $ticket->price,
            'currency' => 'UAH',
            'description' => "{$event->name} $eventDate - $ticket->name",
            'order_id' => "{$this->environment}_order_{$order->id}",
            'language' => 'uk',
            //TODO set
            // 'server_url'
        ];

        $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE);

        return base64_encode($dataJson);
    }

    public function getSignature(string $base64Data): string
    {
        $shaEncodedSignature = sha1($this->privateKey . $base64Data . $this->privateKey, true);

        return base64_encode($shaEncodedSignature);
    }
}
