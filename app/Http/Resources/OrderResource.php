<?php

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public $resource = Order::class;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Order $this */
        return [
            'id' => $this->id,
            'name' => $this->ticket->name,
            'price' => $this->ticket->price,
            'event' => EventResource::make($this->ticket->event),
        ];
    }
}
