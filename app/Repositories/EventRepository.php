<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\EventModel;
use Illuminate\Database\Eloquent\Collection;

class EventRepository
{
    const PAGE_SIZE = 100;

    public function getEventsByFilters(array $filters): Collection
    {
        $page = $filters['page'] ?? 1;

        return EventModel::with(['city', 'eventType'])
            ->where([
                'city_id' => $filters['city_id'],
            ])
            ->when(isset($filters['event_type_id']), function ($query) use ($filters) {
                return $query->where('event_type_id', $filters['event_type_id']);
            })
            ->when(isset($filters['query']), function ($query) use ($filters) {
                return $query->where('name', 'like', '%' . $filters['query'] . '%'); // TODO optimize
            })
            ->when(isset($filters['date_from']), function ($query) use ($filters) {
                return $query->whereDate('date', '>=', $filters['date_from']);
            })
            ->when(isset($filters['date_to']), function ($query) use ($filters) {
                return $query->whereDate('date', '<=', $filters['date_to']);
            })
            ->withMin('tickets', 'price')
            ->limit(self::PAGE_SIZE)
            ->offset(($page - 1) * self::PAGE_SIZE)
            ->get();
    }
}
