<?php
declare(strict_types=1);

namespace Tests\Feature\Events;

use Carbon\Carbon;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GetAllEventsTest extends TestCase
{
    private const EXPECTED_STRUCTURE = [
        'data' => [
            '*' => [
                'id',
                'event_type' => [
                    'id',
                    'name',
                ],
                'city' => [
                    'id',
                    'name',
                ],
                'name',
                'date',
                'image',
                'description',
                'notes',
                'tickets_min_price',
                'created_at',
                'updated_at',
            ],
        ],
    ];

    public function testGetWithCityFilter(): void
    {
        $response = $this->makeRequest([
            'city_id' => 1,
        ]);

        $response
            ->assertJsonStructure(self::EXPECTED_STRUCTURE)
            ->assertStatus(Response::HTTP_OK);

        $this->assertCount(6, $response->json('data'));
    }

    public function testGetWithCityAndEventTypeFilters(): void
    {
        $response = $this->makeRequest([
            'city_id' => 1,
            'event_type_id' => 1,
        ]);

        $response
            ->assertJsonStructure(self::EXPECTED_STRUCTURE)
            ->assertStatus(Response::HTTP_OK);

        $this->assertCount(2, $response->json('data'));
    }

    public function testGetWithCityAndQueryFilters(): void
    {
        $response = $this->makeRequest([
            'city_id' => 1,
            'query' => 'Вогняна Мелодія',
        ]);

        $response
            ->assertJsonStructure(self::EXPECTED_STRUCTURE)
            ->assertStatus(Response::HTTP_OK);

        $this->assertCount(1, $response->json('data'));
    }

    public function testGetWithCityAndDateFilters(): void
    {
        $today = Carbon::today();

        $response = $this->makeRequest([
            'city_id' => 1,
            'date_from' => $today->copy()->addDays(19)->format('Y-m-d'),
            'date_to' => $today->copy()->addDays(21)->format('Y-m-d'),
        ]);

        $response
            ->assertJsonStructure(self::EXPECTED_STRUCTURE)
            ->assertStatus(Response::HTTP_OK);

        $this->assertCount(2, $response->json('data'));
    }

    private function makeRequest(array $params = []): TestResponse
    {
        $queryString = http_build_query($params);

        return $this->get("/events?$queryString", []);
    }
}
