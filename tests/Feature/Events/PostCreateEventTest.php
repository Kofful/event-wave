<?php
declare(strict_types=1);

namespace Feature\Events;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PostCreateEventTest extends TestCase
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

    private array $validRequest;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->validRequest = [
            'city_id' => 1,
            'event_type_id' => 1,
            'name' => 'Тестова назва',
            'image' => UploadedFile::fake()->create('test.jpg', 1024 * 5),
            'date' => (new Carbon())->addDays(2)->format('Y-m-d H:i'),
            'description' => 'Тестовий опис.',
            'notes' => 'Тестові зауваження.',
            'tickets' => [
                [
                    'name' => 'Тестовий базовий квиток',
                    'price' => 200,
                    'quantity' => 20,
                ],
                [
                    'name' => 'Тестовий VIP-квиток',
                    'price' => 500,
                    'quantity' => 10,
                ],
            ],
        ];
    }

    public function testUserIsUnauthorized(): void
    {
        $this->makeRequest()
            ->assertJson([
                'message' => 'Ця дія вимагає авторизації.',
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUserIsVisitor(): void
    {
        $this->actingAsVisitor()
            ->makeRequest()
            ->assertJson([
                'message' => 'Ця дія не доступна цьому користувачу.',
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testUserIsUnapprovedManager(): void
    {
        $this->actingAsUnapprovedManager()
            ->makeRequest()
            ->assertJson([
                'message' => 'Ця дія не доступна цьому користувачу.',
            ])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testEmptyRequestData(): void
    {
        $this->actingAsApprovedManager()
            ->makeRequest()
            ->assertJson([
                'errors' => [
                    'city_id' => ["Параметр місто є обов'язковим."],
                    'event_type_id' => ["Параметр тип події є обов'язковим."],
                    'name' => ["Параметр назва є обов'язковим."],
                    'image' => ["Параметр зображення є обов'язковим."],
                    'date' => ["Параметр дата є обов'язковим."],
                    'tickets' => ["Параметр квитки є обов'язковим."],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCityIdIsNotInteger(): void
    {
        $requestData = [
            ...$this->validRequest,
            'city_id' => 'abc',
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'city_id' => ['Параметр місто має бути числом.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCityIdIsZero(): void
    {
        $requestData = [
            ...$this->validRequest,
            'city_id' => 0,
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'city_id' => ['Параметр місто має бути більше або дорівнювати 1.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCityIdDoesNotExist(): void
    {
        $requestData = [
            ...$this->validRequest,
            'city_id' => 100000,
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'city_id' => ['Параметр місто не відповідає жодному існуючому місту.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testEventTypeIdIsNotInteger(): void
    {
        $requestData = [
            ...$this->validRequest,
            'event_type_id' => 'abc',
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'event_type_id' => ['Параметр тип події має бути числом.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testEventTypeIdIsZero(): void
    {
        $requestData = [
            ...$this->validRequest,
            'event_type_id' => 0,
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'event_type_id' => ['Параметр тип події має бути більше або дорівнювати 1.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testEventTypeIdDoesNotExist(): void
    {
        $requestData = [
            ...$this->validRequest,
            'event_type_id' => 100000,
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'event_type_id' => ['Параметр тип події не відповідає жодному існуючому типу.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testNameIsTooLong(): void
    {
        $requestData = [
            ...$this->validRequest,
            'name' => str_repeat('а', 65),
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'name' => ["Параметр назва має бути не довше 64 символів."],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testNameImageIsNotImageFile(): void
    {
        $requestData = [
            ...$this->validRequest,
            'image' => UploadedFile::fake()->create('test.mp4', 1024 * 5),
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'image' => ['Параметр зображення має бути зображенням.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testNameImageIsTooBig(): void
    {
        $requestData = [
            ...$this->validRequest,
            'image' => UploadedFile::fake()->create('test.jpg', 1024 * 11),
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'image' => ['Параметр зображення має бути не більше 10 МБ.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDateIsNotDate(): void
    {
        $requestData = [
            ...$this->validRequest,
            'date' => 'abc',
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'date' => ['Параметр дата має бути датою.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDateHasWrongFormat(): void
    {
        $requestData = [
            ...$this->validRequest,
            'date' => '2030-01-05',
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'date' => ['Формат дати має бути Y-m-d H:i.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDateIsToday(): void
    {
        $requestData = [
            ...$this->validRequest,
            'date' => (new Carbon())->format('Y-m-d H:i'),
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'date' => ['Параметр дата має бути після сьогоднішньої дати.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testTicketsIsNotArray(): void
    {
        $requestData = [
            ...$this->validRequest,
            'tickets' => 'abc',
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'tickets' => ['Параметр квитки має бути масивом.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testTicketsArrayIsTooLong(): void
    {
        $ticketArray = [];

        for ($i = 1; $i <= 11; $i++) {
            $ticketArray[] = [
                'name' => "Квиток $i",
                'price' => 200,
                'quantity' => 20,
            ];
        }

        $requestData = [
            ...$this->validRequest,
            'tickets' => $ticketArray,
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'tickets' => ['Кількість квитків повинна бути не більше 10.'],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testTicketIsEmpty(): void
    {
        $requestData = [
            ...$this->validRequest,
            'tickets' => [[]],
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'tickets.0.name' => [
                        "Параметр назва квитка є обов'язковим",
                    ],
                    'tickets.0.price' => [
                        "Параметр ціна квитка є обов'язковим.",
                    ],
                    'tickets.0.quantity' => [
                        "Параметр кількість квитків є обов'язковим.",
                    ],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testTicketNameIsTooLong(): void
    {
        $requestData = [
            ...$this->validRequest,
            'tickets' => [[
                'name' => str_repeat('а', 33),
                'price' => 200,
                'quantity' => 20,
            ]],
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'tickets.0.name' => [
                        'Параметр назва квитка має бути не довше 32 символів.',
                    ],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testTicketNamesAreDuplicated(): void
    {
        $requestData = [
            ...$this->validRequest,
            'tickets' => [
                [
                    'name' => 'Тестова назва',
                    'price' => 200,
                    'quantity' => 20,
                ],
                [
                    'name' => 'Тестова назва',
                    'price' => 200,
                    'quantity' => 20,
                ],
            ],
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'tickets.0.name' => [
                        'Параметр назва квитка має дублікати.',
                    ],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testTicketPriceIsNotInteger(): void
    {
        $requestData = [
            ...$this->validRequest,
            'tickets' => [[
                'name' => 'Тестова назва',
                'price' => 'abc',
                'quantity' => 20,
            ]],
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'tickets.0.price' => [
                        'Параметр ціна квитка має бути числом.',
                    ],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testTicketPriceIsTooLow(): void
    {
        $requestData = [
            ...$this->validRequest,
            'tickets' => [[
                'name' => 'Тестова назва',
                'price' => 0,
                'quantity' => 20,
            ]],
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'tickets.0.price' => [
                        'Параметр ціна квитка має бути не менше за 1.',
                    ],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testTicketPriceIsTooHigh(): void
    {
        $requestData = [
            ...$this->validRequest,
            'tickets' => [[
                'name' => 'Тестова назва',
                'price' => 100000,
                'quantity' => 20,
            ]],
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'tickets.0.price' => [
                        'Параметр ціна квитка має бути не більше за 99999.',
                    ],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testTicketQuantityIsNotInteger(): void
    {
        $requestData = [
            ...$this->validRequest,
            'tickets' => [[
                'name' => 'Тестова назва',
                'price' => 200,
                'quantity' => 'abc',
            ]],
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'tickets.0.quantity' => [
                        'Параметр кількість квитків має бути числом.',
                    ],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testTicketQuantityIsTooLow(): void
    {
        $requestData = [
            ...$this->validRequest,
            'tickets' => [[
                'name' => 'Тестова назва',
                'price' => 200,
                'quantity' => 0,
            ]],
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'tickets.0.quantity' => [
                        'Параметр кількість квитків має бути не менше за 1.',
                    ],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testTicketQuantityIsTooHigh(): void
    {
        $requestData = [
            ...$this->validRequest,
            'tickets' => [[
                'name' => 'Тестова назва',
                'price' => 200,
                'quantity' => 10000,
            ]],
        ];

        $this->actingAsApprovedManager()
            ->makeRequest($requestData)
            ->assertJson([
                'errors' => [
                    'tickets.0.quantity' => [
                        'Параметр кількість квитків має бути не більше за 9999.',
                    ],
                ],
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testEventIsCreatedSuccessfully(): void
    {
        Storage::fake('event_images');

        $this->actingAsApprovedManager()
            ->makeRequest($this->validRequest)
            ->assertJsonStructure(self::EXPECTED_STRUCTURE)
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('events', [
            'name' => 'Тестова назва',
            'description' => 'Тестовий опис.',
            'notes' => 'Тестові зауваження.',
        ]);
        $this->assertDatabaseHas('tickets', $this->validRequest['tickets'][0]);
        $this->assertDatabaseHas('tickets', $this->validRequest['tickets'][1]);
    }

    private function makeRequest(array $requestData = []): TestResponse
    {
        return $this->post('events', $requestData);
    }
}
