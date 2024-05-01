<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CitySeeder::class);
        $this->call(EventTypeSeeder::class);
        $this->call(EventSeeder::class);
    }
}
