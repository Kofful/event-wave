<?php
declare(strict_types=1);

namespace Tests\Unit\Requests;

use App\Http\Requests\GetAllEventsRequest;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class GetAllEventsRequestTest extends TestCase
{
    public function testCityIdIsNotProvided(): void
    {
        $expectedMessages = [
            'city_id' => [
                "Параметр місто є обов'язковим.",
            ],
        ];

        $this->assertValidationException([], $expectedMessages);
    }

    public function testCityIdIsNotInteger(): void
    {
        $requestData = [
            'city_id' => 'str',
        ];

        $expectedMessages = [
            'city_id' => [
                'Параметр місто має бути числом.',
            ],
        ];

        $this->assertValidationException($requestData,  $expectedMessages);
    }

    public function testCityIdIsNegative(): void
    {
        $requestData = [
            'city_id' => -1,
        ];

        $expectedMessages = [
            'city_id' => [
                'Параметр місто має бути більше або дорівнювати 1.',
            ],
        ];

        $this->assertValidationException($requestData,  $expectedMessages);
    }

    public function testEventTypeIdIsNotInteger(): void
    {
        $requestData = [
            'city_id' => 1,
            'event_type_id' => 'str',
        ];

        $expectedMessages = [
            'event_type_id' => [
                'Параметр тип події має бути числом.',
            ],
        ];

        $this->assertValidationException($requestData, $expectedMessages);
    }

    public function testEventTypeIdIsNegative(): void
    {
        $requestData = [
            'city_id' => 1,
            'event_type_id' => -1,
        ];

        $expectedMessages = [
            'event_type_id' => [
                'Параметр тип події має бути більше або дорівнювати 1.',
            ],
        ];

        $this->assertValidationException($requestData, $expectedMessages);
    }

    public function testQueryIsTooLong(): void
    {
        $requestData = [
            'city_id' => 1,
            'query' => str_repeat('a', 260),
        ];

        $expectedMessages = [
            'query' => [
                'Параметр пошуку має бути не довше 255 символів.',
            ],
        ];

        $this->assertValidationException($requestData, $expectedMessages);
    }

    public function testPageIsNotInteger(): void
    {
        $requestData = [
            'city_id' => 1,
            'page' => 'str',
        ];

        $expectedMessages = [
            'page' => [
                'Параметр сторінка має бути числом.',
            ],
        ];

        $this->assertValidationException($requestData, $expectedMessages);
    }

    public function testPageIsNegative(): void
    {
        $requestData = [
            'city_id' => 1,
            'page' => -1,
        ];

        $expectedMessages = [
            'page' => [
                'Параметр сторінка має бути більше або дорівнювати 1.',
            ],
        ];

        $this->assertValidationException($requestData, $expectedMessages);
    }

    public function testDateFromIsNotDate(): void
    {
        $requestData = [
            'city_id' => 1,
            'date_from' => -1,
        ];

        $expectedMessages = [
            'date_from' => [
                'Параметр має бути датою.',
            ],
        ];

        $this->assertValidationException($requestData, $expectedMessages);
    }

    public function testDateToIsNotDate(): void
    {
        $requestData = [
            'city_id' => 1,
            'date_to' => -1,
        ];

        $expectedMessages = [
            'date_to' => [
                'Параметр має бути датою.',
            ],
        ];

        $this->assertValidationException($requestData, $expectedMessages);
    }

    public function testAllParametersAreValid(): void
    {
        $requestData = [
            'city_id' => 1,
            'event_type_id' => 1,
            'query' => 'str',
            'page' => 1,
        ];

        $request = new GetAllEventsRequest($requestData);
        $request->setContainer($this->app);
        $request->setRedirector($this->app->make(Redirector::class));

        $request->validateResolved();

        $this->assertEquals($requestData, $request->validated());
    }

    private function assertValidationException(array $requestData, array $expectedMessages): void
    {
        $errors = [];

        try {
            $request = new GetAllEventsRequest($requestData);
            $request->setContainer($this->app);
            $request->setRedirector($this->app->make(Redirector::class));

            $request->validateResolved();
        } catch (ValidationException $e) {
            $errors = $e->errors();
        }

        $this->assertEquals($expectedMessages, $errors);
    }
}
