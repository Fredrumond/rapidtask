<?php

namespace App\Repositories;

use App\Domain\TokenDomain;
use App\Models\User;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;

class TokenEloquentRepository
{
    public function revokeAll(User $user): void
    {
        $user->tokens()->delete();
    }

    public function create(User $user): NewAccessToken
    {
        return $user->createToken(TokenDomain::TOKEN_NAME);
    }

    public function hasActive(User $user): bool
    {
        return $user->tokens()
            ->where('name', TokenDomain::TOKEN_NAME)
            ->exists();
    }

    public function getActive(User $user): ?PersonalAccessToken
    {
        return $user->tokens()
            ->where('name', TokenDomain::TOKEN_NAME)
            ->latest('id')
            ->first();
    }
}
