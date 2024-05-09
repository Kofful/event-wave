<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Ticket;

class TicketRepository
{
    public function createTicketsByEventId(int $eventId, array $tickets): void
    {
        $ticketsData = array_map(function ($ticket) use ($eventId) {
            $ticket['event_id'] = $eventId;

            return $ticket;
        }, $tickets);

        Ticket::upsert($ticketsData, ['event_id', 'name']);
    }
}
