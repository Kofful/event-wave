<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property bool $is_approved
 *
 * @property UserRole $role
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    public const VISITOR_ROLE_ID = 1;
    public const MANAGER_ROLE_ID = 2;
    public const VISITOR_ROLE = 'VISITOR';
    public const MANAGER_ROLE = 'MANAGER';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'is_approved',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(UserRole::class);
    }
}
