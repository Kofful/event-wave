<?php
declare(strict_types=1);

namespace Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostLoginTest extends TestCase
{
    public function testEmptyRequestData(): void
    {
        $this->makeRequest()
            ->assertJson([
                'errors' => [
                    'email' => ["Параметр е-мейл є обов'язковим."],
                    'password' => ["Параметр пароль є обов'язковим."]
                ]
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testEmailIsInvalid(): void
    {
        $requestData = [
            'email' => 'invalid',
            'password' => 'password',
        ];

        $this->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'email' => ['Параметр е-мейл має неправильний формат.'],
                ]
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testWrongEmail(): void
    {
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);

        $requestData = [
            'email' => 'wrong@test.com',
            'password' => 'password',
        ];

        $this->makeRequest($requestData)
            ->assertJson([
                'message' => 'Неправильний e-мейл або пароль.',
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testWrongPassword(): void
    {
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);

        $requestData = [
            'email' => 'test@test.com',
            'password' => 'wrong',
        ];

        $this->makeRequest($requestData)
            ->assertJson([
                'message' => 'Неправильний e-мейл або пароль.',
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUserIsNotApproved(): void
    {
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
            'is_approved' => false,
        ]);

        $requestData = [
            'email' => 'test@test.com',
            'password' => 'password',
        ];

        $this->makeRequest($requestData)
            ->assertJson([
                'message' => 'Користувач не підтверджений.',
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testSuccessfulLogin(): void
    {
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);

        $requestData = [
            'email' => 'test@test.com',
            'password' => 'password',
        ];

        $this->makeRequest($requestData)
            ->assertJsonStructure([
                'status',
                'token',
                'type',
                'user' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'role',
                ],
            ])
            ->assertStatus(Response::HTTP_OK);
    }

    private function makeRequest(array $data = []): TestResponse
    {
        return $this->post('/login', $data);
    }
}
