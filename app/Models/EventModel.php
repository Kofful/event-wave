<?php
declare(strict_types=1);

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property DateTime $date
 * @property string $image
 * @property string $description
 * @property string $notes
 * @property int $owner_id
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property ?int $tickets_min_price
 *
 * @property EventType $eventType
 * @property City $city
 * @property Collection<Ticket> $tickets
 */
class EventModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'event_type_id',
        'owner_id',
        'name',
        'date',
        'image',
        'description',
        'notes',
    ];

    protected $table = 'events';

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'event_id');
    }
}
