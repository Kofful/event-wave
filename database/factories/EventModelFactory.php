<?php
declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventModel>
 */
class EventModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_type_id' => rand(1, 3),
            'city_id' => rand(1, 5),
            'name' => $this->faker->name(),
            'date' => $this->faker->date('Y-m-d H:m:s'),
            'image' => 'test.jpg',
            'description' => $this->faker->text(),
            'notes' => $this->faker->text(),
        ];
    }
}
