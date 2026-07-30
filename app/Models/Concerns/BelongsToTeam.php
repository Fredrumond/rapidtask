<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTeam
{
    protected static function bootBelongsToTeam(): void
    {
        static::addGlobalScope('team', function (Builder $builder) {
            $table = $builder->getModel()->getTable();

            TeamScope::applyTimeIdFilter($builder, "{$table}.time_id");
        });
    }
}
