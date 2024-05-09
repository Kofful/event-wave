<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

class Authenticate extends Middleware
{
    protected function authenticate($request, array $guards)
    {
        /** @var User $authorizedUser */
        $authorizedUser = auth()->user();

        if (!$authorizedUser) {
            throw new UnauthorizedException();
        }

        parent::authenticate($request, $guards);


        if (!$authorizedUser->is_approved) {
            $this->unauthenticated($request, $guards);
        }
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }
}
