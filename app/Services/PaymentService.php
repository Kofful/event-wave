<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use LiqPay;
use stdClass;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Throwable;

class PaymentService
{
    private string $privateKey;
    private string $publicKey;
    private string $serverUrl;
    private string $environment;
    private LiqPay $liqPay;

    public function __construct()
    {
        $this->privateKey = config('payments.liqpay.private_key');
        $this->publicKey = config('payments.liqpay.public_key');
        $this->serverUrl = config('payments.liqpay.server_url');
        $this->environment = config('app.env');
        $this->liqPay = new LiqPay($this->publicKey, $this->privateKey);
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
            'end_date' => Carbon::createFromTimestampMs($data['end_date']),
            'liqpay_payment_id' => $data['payment_id'],
        ]);
    }

    public function refund(Order $order): bool
    {
        try {
            /** @var stdClass $response */
            $response = $this->liqPay->api('request', [
                'action' => 'refund',
                'version' => 3,
                'order_id' => "{$this->environment}_order_{$order->id}",
            ]);

            Log::info("\nLiqPay refund response:\n" . json_encode($response, JSON_UNESCAPED_UNICODE));

            $isSucceeded = $response->status === 'reversed';

            if ($isSucceeded) {
                $order->update([
                    'order_status_id' => Order::REFUNDED_STATUS_ID,
                ]);
            }

            return $isSucceeded;
        } catch (Throwable $e) {
            Log::error("Caught an error while making request to LiqPay API: {$e->getMessage()}");
            throw new ServiceUnavailableHttpException(null, "Виникла помилка під час з'єднання з сервісом оплати.");
        }
    }
}
