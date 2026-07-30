<?php

namespace App\Support;

class CurrentTeam
{
    public const SESSION_KEY = 'current_time_id';

    public static function id(): ?int
    {
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

    public static function clear(): void
    {
        self::set(null);
    }
}
