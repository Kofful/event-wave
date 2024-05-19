<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class PaymentService
{
    private string $privateKey;
    private string $publicKey;
    private string $serverUrl;
    private string $environment;

    public function __construct()
    {
        $this->privateKey = config('payments.liqpay.private_key');
        $this->publicKey = config('payments.liqpay.public_key');
        $this->serverUrl = config('payments.liqpay.server_url');
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
        ];

        if (env('APP_ENV') !== 'local') {
            $data['server_url'] = $this->serverUrl;
        }

        $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE);

        return base64_encode($dataJson);
    }

    public function getSignature(string $base64Data): string
    {
        $shaEncodedSignature = sha1($this->privateKey . $base64Data . $this->privateKey, true);

        return base64_encode($shaEncodedSignature);
    }

    public function updateOrderStatus(string $base64Data): void
    {
        $data = json_decode(base64_decode($base64Data), true);

        // retrieve number from string like "environment_order_42"
        $orderId = Arr::last(explode('_', $data['order_id']));
        $statusId = $data['status'] === 'success' ? Order::SUCCESS_STATUS_ID : Order::FAILED_STATUS_ID;

        /** @var Order $order */
        $order = Order::query()->findOrFail($orderId);

        $order->update([
            'order_status_id' => $statusId,
            'end_date' => $data['end_date'],
            'liqpay_payment_id' => $data['payment_id'],
        ]);
    }
}
