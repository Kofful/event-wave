<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AuthorizedUserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'message' => 'Неправильний e-мейл або пароль.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        /** @var User $authorizedUser */
        $authorizedUser = Auth::user();

        if (!$authorizedUser->is_approved) {
            return response()->json([
                'message' => 'Користувач не підтверджений.',
            ], Response::HTTP_FORBIDDEN);
        }

        $resource = new AuthorizedUserResource($authorizedUser);
        $resource->additional(['token' => $token]);

        return response()->json($resource);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->userRepository->createUser($request->all());

        if (!$user->is_approved) {
            return response()->json([
                'status' => 'success',
            ]);
        }

        $token = Auth::login($user);

        $resource = new AuthorizedUserResource($user);
        $resource->additional(['token' => $token]);

        return response()->json($resource);
    }
}
