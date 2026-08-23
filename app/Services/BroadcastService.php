<?php

namespace App\Services;

use Pusher\Pusher;
use Illuminate\Support\Facades\Log;

class BroadcastService
{
    private static ?Pusher $pusher = null;

    private static function getPusher(): ?Pusher
    {
        if (self::$pusher !== null) {
            return self::$pusher;
        }

        $appId = env('PUSHER_APP_ID');
        $appKey = env('PUSHER_APP_KEY');
        $appSecret = env('PUSHER_APP_SECRET');
        $cluster = env('PUSHER_APP_CLUSTER', 'mt1');

        if (!$appId || !$appKey || !$appSecret) {
            return null;
        }

        try {
            self::$pusher = new Pusher($appKey, $appSecret, $appId, [
                'cluster' => $cluster,
                'useTLS' => true,
            ]);
            return self::$pusher;
        } catch (\Throwable $e) {
            Log::warning('Pusher initialization failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Broadcast an event to a restaurant room.
     * Channel: restaurant.{ownerId}
     * Event: order:new, order:updated, service:request, table:reset
     */
    public static function emitToRestaurant(string $ownerId, string $event, mixed $data): void
    {
        $pusher = self::getPusher();
        if ($pusher) {
            try {
                $pusher->trigger("restaurant.{$ownerId}", $event, $data);
            } catch (\Throwable $e) {
                Log::warning("Pusher trigger failed for event '{$event}': " . $e->getMessage());
            }
        }
    }
}
