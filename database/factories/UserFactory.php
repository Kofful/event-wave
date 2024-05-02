<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker->email(),
            'first_name' => $this->faker->name(),
            'last_name' => $this->faker->name(),
            'password' => Hash::make($this->faker->password()),
            'role_id' => User::VISITOR_ROLE_ID,
            'is_approved' => true,
        ];
    }
}
