<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    public function run(): void
    {
        $eventTypes = [
            [
                'id' => 1,
                'name' => 'Концерт',
            ],
            [
                'id' => 2,
                'name' => 'Стендап',
            ],
            [
                'id' => 3,
                'name' => 'Для дітей',
            ],
        ];

        EventType::query()->upsert($eventTypes, ['id']);
    }
}
