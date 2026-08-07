<?php

namespace App\Support;

use App\Models\Time;

class CurrentTeam
{
    public const SESSION_KEY = 'current_time_id';

    public const CONTA_SESSION_KEY = 'current_conta_id';

    private static bool $hasRequestOverride = false;

    private static ?int $requestTimeId = null;

    private static ?int $requestContaId = null;

    public static function id(): ?int
    {
        if (self::$hasRequestOverride) {
            return self::$requestTimeId;
        }

        $id = session(self::SESSION_KEY);

        return $id !== null ? (int) $id : null;
    }

    public static function contaId(): ?int
    {
        if (self::$hasRequestOverride) {
            return self::$requestContaId;
        }

        $id = session(self::CONTA_SESSION_KEY);

        return $id !== null ? (int) $id : null;
    }

    public static function set(?int $timeId): void
    {
        if ($timeId === null) {
            session()->forget([self::SESSION_KEY, self::CONTA_SESSION_KEY]);

            return;
        }

        session([
            self::SESSION_KEY => $timeId,
            self::CONTA_SESSION_KEY => self::resolveContaId($timeId),
        ]);
    }

    public static function setForRequest(?int $timeId): void
    {
        self::$hasRequestOverride = true;
        self::$requestTimeId = $timeId;
        self::$requestContaId = self::resolveContaId($timeId);
    }

    public static function clearRequestOverride(): void
    {
        self::$hasRequestOverride = false;
        self::$requestTimeId = null;
        self::$requestContaId = null;
    }

    public static function clear(): void
    {
        self::set(null);
        self::clearRequestOverride();
    }

    private static function resolveContaId(?int $timeId): ?int
    {
        if ($timeId === null) {
            return null;
        }

        $contaId = Time::withoutGlobalScopes()
            ->whereKey($timeId)
            ->value('conta_id');

        return $contaId !== null ? (int) $contaId : null;
    }
}
