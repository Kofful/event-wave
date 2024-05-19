<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Models\Ticket;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
    ) {
    }

    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        /** @var Ticket $ticket */
        $ticket = Ticket::query()->findOrFail($request->input('ticket_id'));

        /** @var Order $order */
        $order = Order::query()->create([
            'ticket_id' => $ticket->id,
            'email' => $request->input('email'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'order_status_id' => Order::PENDING_STATUS_ID,
        ]);

        $requestData = $this->paymentService->getData($order);
        $signature = $this->paymentService->getSignature($requestData);

        return response()->json([
            'data' => $requestData,
            'signature' => $signature,
        ]);
    }
}
