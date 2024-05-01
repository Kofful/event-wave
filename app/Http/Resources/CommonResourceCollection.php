<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommonResourceCollection extends AnonymousResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => $this->collection->toArray(),
        ];
    }
}
