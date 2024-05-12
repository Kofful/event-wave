<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\GetAllEventsRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\CommonResourceCollection;
use App\Http\Resources\EventDetailsResource;
use App\Http\Resources\EventResource;
use App\Models\EventModel;
use App\Repositories\EventRepository;
use App\Repositories\TicketRepository;
use App\Services\EventImageService;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly EventImageService $eventImageService,
        private readonly TicketRepository $ticketRepository,
    ) {
    }

    public function getAllEvents(GetAllEventsRequest $request): JsonResponse
    {
        $filters = $request->only(['city_id', 'event_type_id', 'query', 'page', 'date_from', 'date_to']);
        $events = $this->eventRepository->getEventsByFilters($filters);

        return response()->json(new CommonResourceCollection($events, EventResource::class));
    }

    public function create(CreateEventRequest $request): JsonResponse
    {
        $requestFile = $request->file('image');
        $eventData = $request->only(['city_id', 'event_type_id', 'name', 'date', 'description', 'notes']);
        $eventData['image'] = $this->eventImageService->getFileName($requestFile);
        $eventData['owner_id'] = auth()->user()->id;

        /** @var EventModel $event */
        $event = EventModel::create($eventData);

        $this->ticketRepository->createTicketsByEventId($event->id, $request->input('tickets'));

        $this->eventImageService->saveFile($requestFile, $event->image);

        $event->load(['city', 'eventType', 'tickets']);
        return response()->json(new EventDetailsResource($event));
    }

    public function update(UpdateEventRequest $request, EventModel $event): JsonResponse
    {
        $requestFile = $request->file('image');
        $dataToUpdate = $request->only(['city_id', 'event_type_id', 'name', 'date', 'description', 'notes']);

        if ($requestFile) {
            $dataToUpdate['image'] = $this->eventImageService->getFileName($requestFile);

            $this->eventImageService->deleteFile($event->image);
            $this->eventImageService->saveFile($requestFile, $dataToUpdate['image']);
        }

        $event->update($dataToUpdate);

        $event->load(['city', 'eventType', 'tickets']);
        return response()->json(new EventDetailsResource($event));
    }
}
