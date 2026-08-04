<?php

namespace App\Support;

class CurrentTeam
{
    public const SESSION_KEY = 'current_time_id';

    public const HEADER_NAME = 'X-Time-Id';

    private static bool $hasRequestOverride = false;

    private static ?int $requestTimeId = null;

    public static function id(): ?int
    {
        if (self::$hasRequestOverride) {
            return self::$requestTimeId;
        }

        $id = session(self::SESSION_KEY);

        return $id !== null ? (int) $id : null;
    }

    public static function set(?int $timeId): void
    {
        if ($timeId === null) {
            session()->forget(self::SESSION_KEY);

            return;
        }

        session([self::SESSION_KEY => $timeId]);
    }

    public static function setForRequest(?int $timeId): void
    {
        self::$hasRequestOverride = true;
        self::$requestTimeId = $timeId;
    }

    public static function clearRequestOverride(): void
    {
        self::$hasRequestOverride = false;
        self::$requestTimeId = null;
    }

    public static function clear(): void
    {
        self::set(null);
        self::clearRequestOverride();
    }
}
