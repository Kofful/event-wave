<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\CommonResourceCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Ticket;
use App\Repositories\OrderRepository;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly OrderRepository $orderRepository,
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

    public function updateOrderStatus(UpdateOrderStatusRequest $request): JsonResponse
    {
        $signature = $request->input('signature');
        $data = $request->input('data');
        $generatedSignature = $this->paymentService->getSignature($data);

        Log::debug("\nVerifying signature: $signature\nReceived data: $data\nGenerated signature: $generatedSignature");

        if ($signature !== $generatedSignature) {
            return response()->json([
                'error' => 'Неправильний підпис.',
            ], Response::HTTP_FORBIDDEN);
        }

        $this->paymentService->updateOrderStatus($data);

        return response()->json([
            'status' => 'success',
        ]);
    }

    public function getUserOrders(): JsonResponse
    {
        // TODO add tests
        $userEmail = auth()->user()->email;

        $orders = $this->orderRepository->getUserOrders($userEmail);

        return response()->json(new CommonResourceCollection($orders, OrderResource::class));
    }
}
