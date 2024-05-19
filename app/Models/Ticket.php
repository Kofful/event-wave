<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $name
 * @property int $price
 * @property int $quantity
 *
 * @property EventModel $event
 */
class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'price',
        'quantity',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(EventModel::class);
    }
}
