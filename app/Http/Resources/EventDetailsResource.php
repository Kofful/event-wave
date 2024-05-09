<?php
declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\EventModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventDetailsResource extends JsonResource
{
    public $resource = EventModel::class;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var EventModel $this */
        return [
            'id' => $this->id,
            'event_type' => EventTypeResource::make($this->eventType),
            'city' => CityResource::make($this->city),
            'name' => $this->name,
            'date' => $this->date,
            'image' => config('images.public_path') . $this->image,
            'description' => $this->description,
            'notes' => $this->notes,
            'tickets' => EventTicketResource::collection($this->tickets),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
