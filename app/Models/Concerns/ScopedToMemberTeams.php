<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait ScopedToMemberTeams
{
    protected static function bootScopedToMemberTeams(): void
    {
        static::addGlobalScope('team', function (Builder $builder) {
            TeamScope::applyMemberFilter($builder);
        });
    }
}
