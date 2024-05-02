<?php
declare(strict_types=1);

namespace Feature\Auth;

use App\Models\User;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostRegisterTest extends TestCase
{

    public function testEmptyRequestData(): void
    {
        $this->makeRequest()
            ->assertJson([
                'errors' => [
                    'first_name' => ["Параметр ім'я є обов'язковим."],
                    'last_name' => ["Параметр прізвище є обов'язковим."],
                    'email' => ["Параметр е-мейл є обов'язковим."],
                    'password' => ["Параметр пароль є обов'язковим."]
                ]
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testFirstNameIsInvalid(): void
    {
        $requestData = [
            'first_name' => '123',
            'last_name' => 'Їжак',
            'email' => 'test@test.com',
            'password' => 'password',
        ];

        $this->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'first_name' => ["Параметр ім'я має містити тільки літери."],
                ]
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testFirstNameIsTooLong(): void
    {
        $requestData = [
            'first_name' => str_repeat('Є', 33),
            'last_name' => 'Їжак',
            'email' => 'test@test.com',
            'password' => 'password',
        ];

        $this->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'first_name' => ["Параметр ім'я має бути не довше 32 символів."],
                ]
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testLastNameIsInvalid(): void
    {
        $requestData = [
            'first_name' => 'Василь',
            'last_name' => '123',
            'email' => 'test@test.com',
            'password' => 'password',
        ];

        $this->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'last_name' => ['Параметр прізвище має містити тільки літери.'],
                ]
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testLastNameIsTooLong(): void
    {
        $requestData = [
            'first_name' => 'Василь',
            'last_name' => str_repeat('Ї', 33),
            'email' => 'test@test.com',
            'password' => 'password',
        ];

        $this->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'last_name' => ['Параметр прізвище має бути не довше 32 символів.'],
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

    public function testEmailIsTooLong(): void
    {
        $requestData = [
            'first_name' => 'Василь',
            'last_name' => 'Їжак',
            'email' => str_repeat('t', 120) . '@' . 'test.com',
            'password' => 'password',
        ];

        $this->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'email' => ['Параметр е-мейл має бути не довше 128 символів.'],
                ]
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testEmailIsAlreadyTaken(): void
    {
        User::factory()->create([
            'email' => 'taken@test.com',
        ]);

        $requestData = [
            'first_name' => 'Василь',
            'last_name' => 'Їжак',
            'email' => 'taken@test.com',
            'password' => 'password',
        ];

        $this->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'email' => ['Цей е-мейл вже зайнятий.'],
                ]
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testRoleIsInvalid(): void
    {
        $requestData = [
            'first_name' => 'Василь',
            'last_name' => 'Їжак',
            'email' => 'taken@test.com',
            'password' => 'password',
            'role' => 'INVALID'
        ];

        $this->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'role' => ['Неправильна роль. Роль може бути тільки: VISITOR,MANAGER.'],
                ]
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testPasswordIsTooShort(): void
    {
        $requestData = [
            'first_name' => 'Василь',
            'last_name' => 'Їжак',
            'email' => 'test@test.com',
            'password' => 'pass',
        ];

        $this->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'password' => ['Параметр пароль має бути не менше 6 символів.'],
                ]
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testPasswordIsTooLong(): void
    {
        $requestData = [
            'first_name' => 'Василь',
            'last_name' => 'Їжак',
            'email' => 'test@test.com',
            'password' => str_repeat('p', 129),
        ];

        $this->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'password' => ['Параметр пароль має бути не довше 128 символів.'],
                ]
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testSuccessfulRegistrationForManager(): void
    {
        $requestData = [
            'first_name' => 'Василь',
            'last_name' => 'Їжак',
            'email' => 'test@test.com',
            'password' => 'password',
            'role' => User::MANAGER_ROLE,
        ];

        $this->makeRequest($requestData)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertStatus(Response::HTTP_OK);
    }

    public function testSuccessfulRegistrationForImplicitVisitor(): void
    {
        $requestData = [
            'first_name' => 'Василь',
            'last_name' => 'Їжак',
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

    public function testSuccessfulRegistrationForExplicitVisitor(): void
    {
        $requestData = [
            'first_name' => 'Василь',
            'last_name' => 'Їжак',
            'email' => 'test@test.com',
            'password' => 'password',
            'role' => User::VISITOR_ROLE,
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
        return $this->post('/register', $data);
    }
}
