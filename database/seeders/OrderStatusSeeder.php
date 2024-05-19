<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $orderStatuses = [
            [
                'id' => 1,
                'name' => 'PENDING',
            ],
            [
                'id' => 2,
                'name' => 'SUCCESS',
            ],
            [
                'id' => 3,
                'name' => 'FAILED',
            ],
            [
                'id' => 4,
                'name' => 'REFUNDED',
            ]
        ];

        OrderStatus::query()->upsert($orderStatuses, ['id']);
    }
}
