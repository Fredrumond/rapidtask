<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTeamViaProjeto
{
    protected static function bootBelongsToTeamViaProjeto(): void
    {
        static::addGlobalScope('team', function (Builder $builder) {
            TeamScope::applyProjetoTimeFilter($builder);
        });
    }
}
