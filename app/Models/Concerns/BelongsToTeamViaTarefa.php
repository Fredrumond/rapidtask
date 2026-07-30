<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTeamViaTarefa
{
    protected static function bootBelongsToTeamViaTarefa(): void
    {
        static::addGlobalScope('team', function (Builder $builder) {
            TeamScope::applyTarefaProjetoTimeFilter($builder);
        });
    }
}
