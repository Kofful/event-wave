<?php
declare(strict_types=1);

namespace Tests\Traits;

use App\Models\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;

trait InteractsWithAuthenticationRoles
{
    use InteractsWithAuthentication;

    public function actingAsVisitor(): self
    {
        $user = User::factory()->create([
            'role_id' => User::VISITOR_ROLE_ID,
        ]);

        return $this->actingAs($user);
    }

    public function actingAsUnapprovedManager(): self
    {
        $user = User::factory()->create([
            'role_id' => User::MANAGER_ROLE_ID,
            'is_approved' => false,
        ]);

        return $this->actingAs($user);
    }

    public function actingAsApprovedManager(): self
    {
        $user = User::factory()->create([
            'role_id' => User::MANAGER_ROLE_ID,
            'is_approved' => true,
        ]);

        return $this->actingAs($user);
    }
}
