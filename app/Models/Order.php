<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property Ticket $ticket
 */
class Order extends Model
{
    use HasFactory;

    public const PENDING_STATUS_ID = 1;
    public const SUCCESS_STATUS_ID = 2;
    public const FAILED_STATUS_ID = 3;
    public const REFUNDED_STATUS_ID = 4;

    protected $fillable = [
        'ticket_id',
        'order_status_id',
        'email',
        'first_name',
        'last_name',
        'liqpay_payment_id',
        'end_date',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
