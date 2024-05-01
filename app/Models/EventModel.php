<?php
declare(strict_types=1);

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string name
 * @property DateTime $date
 * @property string $image
 * @property string $description
 * @property string $notes
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @property EventType $eventType
 * @property City $city
 */
class EventModel extends Model
{
    use HasFactory;

    protected $table = 'events';

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
