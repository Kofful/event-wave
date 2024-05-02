<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $userRoles = [
            [
                'id' => 1,
                'role' => 'VISITOR',
            ],
            [
                'id' => 2,
                'role' => 'MANAGER',
            ]
        ];

        UserRole::query()->upsert($userRoles, ['id']);
    }
}
