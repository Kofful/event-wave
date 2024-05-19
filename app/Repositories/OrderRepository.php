<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository
{
    public function getUserOrders(string $userEmail): Collection
    {
        return Order::with([
            'ticket',
            'ticket.event',
        ])->where([
            'email' => $userEmail,
            'order_status_id' => Order::SUCCESS_STATUS_ID,
        ])->get();
    }
}
