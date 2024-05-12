<?php

namespace App\Policies;

use App\Models\EventModel;
use App\Models\User;

class EventPolicy
{
    public function create(User $user): bool
    {
        return $user->is_approved;
    }

    public function update(User $user, EventModel $event): bool
    {
        $isUserEventOwner = $event->owner_id === $user->id;

        return $isUserEventOwner;
    }
}
