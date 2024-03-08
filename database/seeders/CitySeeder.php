<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            [
                'id' => 1,
                'name' => 'Київ',
            ],
            [
                'id' => 2,
                'name' => 'Запоріжжя',
            ],
            [
                'id' => 3,
                'name' => 'Львів',
            ],
            [
                'id' => 4,
                'name' => 'Дніпро',
            ],
            [
                'id' => 5,
                'name' => 'Харків',
            ],
        ];

        City::query()->upsert($cities, ['id']);
    }
}
