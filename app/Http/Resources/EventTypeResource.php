<?php

namespace App\Http\Resources;

use App\Models\EventType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var EventType $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
