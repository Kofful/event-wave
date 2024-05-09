<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class UserHasRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param mixed ...$roles
     * @return mixed
     *
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next, ...$roles): mixed
    {
        /** @var User $authorizedUser */
        $authorizedUser = auth()->user();

        if (!in_array($authorizedUser->role->role, $roles)) {
            throw new AuthenticationException();
        }

        return $next($request);
    }
}
