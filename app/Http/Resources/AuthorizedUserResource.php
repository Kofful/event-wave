<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorizedUserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'status' => 'success',
            'token' => $this->additional['token'],
            'type' => 'bearer',
            'user' => UserResource::make($this->resource),
        ];
    }
}
