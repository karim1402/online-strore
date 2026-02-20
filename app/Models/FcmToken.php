<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $fillable = [
        'device_id',
        'token',
        'tokenable_id',
        'tokenable_type',
        'platform',
    ];

    /**
     * Get the parent tokenable model (User, Admin, Delivery, Vendor).
     */
    public function tokenable()
    {
        return $this->morphTo();
    }

    /**
     * Get all FCM tokens for a specific entity.
     *
     * @param string $type  e.g. 'App\Models\User'
     * @param int    $id
     * @return array
     */
    public static function getTokensForUser(string $type, int $id): array
    {
        return static::where('tokenable_type', $type)
            ->where('tokenable_id', $id)
            ->pluck('token')
            ->toArray();
    }

    /**
     * Get all FCM tokens for multiple entities of the same type.
     *
     * @param string $type  e.g. 'App\Models\Delivery'
     * @param array  $ids
     * @return array
     */
    public static function getTokensForUsers(string $type, array $ids): array
    {
        return static::where('tokenable_type', $type)
            ->whereIn('tokenable_id', $ids)
            ->pluck('token')
            ->toArray();
    }

    /**
     * Get all FCM tokens belonging to Users (for user-specific notifications).
     *
     * @param array|null $userIds  Optional filter by specific user IDs
     * @return array
     */
    public static function getAllUserTokens(?array $userIds = null): array
    {
        $query = static::where('tokenable_type', User::class);

        if ($userIds) {
            $query->whereIn('tokenable_id', $userIds);
        }

        return $query->pluck('token')->toArray();
    }

    /**
     * Get ALL FCM tokens in the table (for broadcast to all devices including guests).
     *
     * @return array
     */
    public static function getAllTokens(): array
    {
        return static::pluck('token')->toArray();
    }
}
