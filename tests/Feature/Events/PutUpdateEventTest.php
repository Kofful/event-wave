<?php
declare(strict_types=1);

namespace Feature\Events;

use App\Models\EventModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PutUpdateEventTest extends TestCase
{
    private const EXPECTED_STRUCTURE = [
        'id',
        'event_type' => [
            'id',
            'name',
        ],
        'city' => [
            'id',
            'name',
        ],
        'owner_id',
        'tickets' => [
            '*' => [
                'id',
                'name',
                'price',
                'quantity',
            ],
        ],
        'name',
        'date',
        'image',
        'description',
        'notes',
        'created_at',
        'updated_at',
    ];

    public function testUserIsUnauthorized(): void
    {
        $event = EventModel::factory()->create();

        $this->makeRequest($event->id)
            ->assertJson([
                'message' => 'Ця дія вимагає авторизації.',
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUserIsVisitor(): void
    {
        $event = EventModel::factory()->create();

        $this->actingAsVisitor()
            ->makeRequest($event->id)
            ->assertJson([
                'message' => 'Ця дія не доступна цьому користувачу.',
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testUserIsUnapprovedManager(): void
    {
        $event = EventModel::factory()->create();

        $this->actingAsUnapprovedManager()
            ->makeRequest($event->id)
            ->assertJson([
                'message' => 'Ця дія не доступна цьому користувачу.',
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testUserIsNotEventOwner(): void
    {
        $eventOwner = User::factory()->create([
            'role_id' => User::MANAGER_ROLE_ID,
            'is_approved' => true,
        ]);
        $event = EventModel::factory()->create([
            'owner_id' => $eventOwner->id,
        ]);

        $requestData = [
            'city_id' => 1,
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($event->id, $requestData)
            ->assertJson([
                'message' => 'Ця дія не доступна цьому користувачу.',
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testEventNotFound(): void
    {
        $requestData = [
            'city_id' => 1,
        ];

        $this->actingAsApprovedManager()
            ->makeRequest(0, $requestData)
            ->assertJson([
                'message' => 'Не вдалось знайти нічого за цим ідентифікатором.',
            ])
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testCityIdIsNotInteger(): void
    {
        $user = User::factory()->create([
            'role_id' => User::MANAGER_ROLE_ID,
            'is_approved' => true,
        ]);
        $event = EventModel::factory()->create([
            'owner_id' => $user->id,
        ]);

        $requestData = [
            'city_id' => 'abc',
        ];

        $this->actingAs($user)
            ->makeRequest($event->id, $requestData)
            ->assertJson([
                'errors' => [
                    'city_id' => ['Параметр місто має бути числом.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCityIdIsZero(): void
    {
        $user = User::factory()->create([
            'role_id' => User::MANAGER_ROLE_ID,
            'is_approved' => true,
        ]);
        $event = EventModel::factory()->create([
            'owner_id' => $user->id,
        ]);

        $requestData = [
            'city_id' => 0,
        ];

        $this->actingAs($user)
            ->makeRequest($event->id, $requestData)
            ->assertJson([
                'errors' => [
                    'city_id' => ['Параметр місто має бути більше або дорівнювати 1.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCityIdDoesNotExist(): void
    {
        $user = User::factory()->create([
            'role_id' => User::MANAGER_ROLE_ID,
            'is_approved' => true,
        ]);
        $event = EventModel::factory()->create([
            'owner_id' => $user->id,
        ]);

        $requestData = [
            'city_id' => 100000,
        ];

        $this->actingAs($user)
            ->makeRequest($event->id, $requestData)
            ->assertJson([
                'errors' => [
                    'city_id' => ['Параметр місто не відповідає жодному існуючому місту.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testEventTypeIdIsNotInteger(): void
    {
        $user = User::factory()->create([
            'role_id' => User::MANAGER_ROLE_ID,
            'is_approved' => true,
        ]);
        $event = EventModel::factory()->create([
            'owner_id' => $user->id,
        ]);

        $requestData = [
            'event_type_id' => 'abc',
        ];

        $this->actingAs($user)
            ->makeRequest($event->id, $requestData)
            ->assertJson([
                'errors' => [
                    'event_type_id' => ['Параметр тип події має бути числом.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testEventTypeIdIsZero(): void
    {
        $user = User::factory()->create([
            'role_id' => User::MANAGER_ROLE_ID,
            'is_approved' => true,
        ]);
        $event = EventModel::factory()->create([
            'owner_id' => $user->id,
        ]);

        $requestData = [
            'event_type_id' => 0,
        ];

        $this->actingAs($user)
            ->makeRequest($event->id, $requestData)
            ->assertJson([
                'errors' => [
                    'event_type_id' => ['Параметр тип події має бути більше або дорівнювати 1.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testEventTypeIdDoesNotExist(): void
    {
        $user = User::factory()->create([
            'role_id' => User::MANAGER_ROLE_ID,
            'is_approved' => true,
        ]);
        $event = EventModel::factory()->create([
            'owner_id' => $user->id,
        ]);

        $requestData = [
            'event_type_id' => 100000,
        ];

        $this->actingAs($user)
            ->makeRequest($event->id, $requestData)
            ->assertJson([
                'errors' => [
                    'event_type_id' => ['Параметр тип події не відповідає жодному існуючому типу.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testNameIsTooLong(): void
    {
        $user = User::factory()->create([
            'role_id' => User::MANAGER_ROLE_ID,
            'is_approved' => true,
        ]);
        $event = EventModel::factory()->create([
            'owner_id' => $user->id,
        ]);

        $requestData = [
            'name' => str_repeat('а', 65),
        ];

        $this->actingAs($user)
            ->makeRequest($event->id, $requestData)
            ->assertJson([
                'errors' => [
                    'name' => ["Параметр назва має бути не довше 64 символів."],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testNameImageIsNotImageFile(): void
    {
        $user = User::factory()->create([
            'role_id' => User::MANAGER_ROLE_ID,
            'is_approved' => true,
        ]);
        $event = EventModel::factory()->create([
            'owner_id' => $user->id,
        ]);

        $requestData = [
            'image' => UploadedFile::fake()->create('test.mp4', 1024 * 5),
        ];

        $this->actingAs($user)
            ->makeRequest($event->id, $requestData)
            ->assertJson([
                'errors' => [
                    'image' => ['Параметр зображення має бути зображенням.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testNameImageIsTooBig(): void
    {
        $user = User::factory()->create([
            'role_id' => User::MANAGER_ROLE_ID,
            'is_approved' => true,
        ]);
        $event = EventModel::factory()->create([
            'owner_id' => $user->id,
        ]);

        $requestData = [
            'image' => UploadedFile::fake()->create('test.jpg', 1024 * 11),
        ];

        $this->actingAs($user)
            ->makeRequest($event->id, $requestData)
            ->assertJson([
                'errors' => [
                    'image' => ['Параметр зображення має бути не більше 10 МБ.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDateIsNotDate(): void
    {
        $user = User::factory()->create([
            'role_id' => User::MANAGER_ROLE_ID,
            'is_approved' => true,
        ]);
        $event = EventModel::factory()->create([
            'owner_id' => $user->id,
        ]);

        $requestData = [
            'date' => 'abc',
        ];

        $this->actingAs($user)
            ->makeRequest($event->id, $requestData)
            ->assertJson([
                'errors' => [
                    'date' => ['Параметр дата має бути датою.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDateHasWrongFormat(): void
    {
        $user = User::factory()->create([
            'role_id' => User::MANAGER_ROLE_ID,
            'is_approved' => true,
        ]);
        $event = EventModel::factory()->create([
            'owner_id' => $user->id,
        ]);

        $requestData = [
            'date' => '2030-01-05',
        ];

        $this->actingAs($user)
            ->makeRequest($event->id, $requestData)
            ->assertJson([
                'errors' => [
                    'date' => ['Формат дати має бути Y-m-d H:i.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDateIsToday(): void
    {
        $user = User::factory()->create([
            'role_id' => User::MANAGER_ROLE_ID,
            'is_approved' => true,
        ]);
        $event = EventModel::factory()->create([
            'date' => (new Carbon())->addDays(5),
            'owner_id' => $user->id,
        ]);

        $requestData = [
            'date' => (new Carbon())->addDays(4)->format('Y-m-d H:i'),
        ];

        $this->actingAs($user)
            ->makeRequest($event->id, $requestData)
            ->assertJson([
                'errors' => [
                    'date' => ['Параметр дата має бути після поточної дати події.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testEventIsUpdatedSuccessfully(): void
    {
        Storage::fake('event_images');

        $user = User::factory()->create([
            'role_id' => User::MANAGER_ROLE_ID,
            'is_approved' => true,
        ]);
        $event = EventModel::factory()->create([
            'owner_id' => $user->id,
        ]);

        $requestData = [
            'city_id' => 1,
            'event_type_id' => 1,
            'name' => 'Тестова назва',
            'image' => UploadedFile::fake()->create('test.jpg', 1024 * 5),
            'date' => (new Carbon())->addDays(2)->format('Y-m-d H:i'),
            'description' => 'Тестовий опис.',
            'notes' => 'Тестові зауваження.',
        ];

        $this->actingAs($user)
            ->makeRequest($event->id, $requestData)
            ->assertJsonStructure(self::EXPECTED_STRUCTURE)
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('events', [
            'name' => 'Тестова назва',
            'description' => 'Тестовий опис.',
            'notes' => 'Тестові зауваження.',
        ]);
    }

    private function makeRequest(int $id, array $requestData = []): TestResponse
    {
        return $this->put("events/$id", $requestData);
    }
}
