<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class CacheLock extends Model
{
    public $timestamps = false;

    /**
     * Attempt to acquire the lock.
     *
     * @param string $key
     * @param int $key_id
     * @param int $ttl_seconds
     * @return bool
     * @throws \Exception
     */
    public static function acquire(string $key, int $key_id = 0, int $ttl_seconds = 600): bool
    {
        $acquired = false;

        try {
            self::query()->insert([
                'key' => $key,
                'key_id' => $key_id,
                'expires_at' => now()->addSeconds($ttl_seconds),
            ]);

            $acquired = true;
        } catch (QueryException $e) {
            $updated = self::query()
                ->where('key', $key)
                ->where('expires_at', '<=', now())
                ->update([
                    'expires_at' => now()->addSeconds($ttl_seconds),
                ]);

            $acquired = $updated >= 1;
        }

        if (rand(1, 5) === 5) {
            retry(5, function () {
                self::query()
                    ->where('expires_at', '<=', now()->subSeconds(5))
                    ->delete();
            });
        }

        return $acquired;
    }

    /**
     * @throws \Exception
     */
    public static function release(string $key, int $key_id = 0)
    {
        retry(5, function () use ($key, $key_id) {
            self::query()
                ->where(['key' => $key, 'key_id' => $key_id])
                // at the same time we can clear expired locks
                ->orWhere('expires_at', '<=', now()->subMinute())
                ->delete();
        });
    }
}
