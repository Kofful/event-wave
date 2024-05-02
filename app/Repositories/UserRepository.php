<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function createUser(array $data): User
    {
        $roleInput = $data['role'] ?? null;
        $isManager = $roleInput === User::MANAGER_ROLE;

        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => $isManager ? User::MANAGER_ROLE_ID : User::VISITOR_ROLE_ID,
            'is_approved' => !$isManager,
        ]);
    }
}
