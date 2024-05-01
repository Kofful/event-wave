<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GetAllEventsRequest;
use App\Http\Resources\CommonResourceCollection;
use App\Http\Resources\EventResource;
use App\Repositories\EventRepository;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {
    }

    public function getAllEvents(GetAllEventsRequest $request): JsonResponse
    {
        $filters = $request->only(['city_id', 'event_type_id', 'query', 'page', 'date_from', 'date_to']);
        $events = $this->eventRepository->getEventsByFilters($filters);

        return response()->json(new CommonResourceCollection($events, EventResource::class));
    }
}
