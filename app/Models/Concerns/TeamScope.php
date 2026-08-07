<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TeamScope
{
    public static function applyTimeIdFilter(Builder $builder, string $column): void
    {
        if (! Auth::check()) {
            return;
        }

        $currentTeamId = current_time_id();

        if ($currentTeamId !== null) {
            $builder->where($column, $currentTeamId);

            return;
        }

        $user = Auth::user();

        if (! $user instanceof User) {
            $builder->whereRaw('1 = 0');

            return;
        }

        $teamIds = $user->timeMembros()->pluck('time_id');

        if ($teamIds->isEmpty()) {
            $builder->whereRaw('1 = 0');

            return;
        }

        $builder->whereIn($column, $teamIds);
    }

    public static function applyProjetoTimeFilter(Builder $builder): void
    {
        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();
        $currentTeamId = current_time_id();

        $builder->whereHas('projeto', function (Builder $query) use ($user, $currentTeamId) {
            if ($currentTeamId !== null) {
                $query->where('time_id', $currentTeamId);

                return;
            }

            if (! $user instanceof User) {
                $query->whereRaw('1 = 0');

                return;
            }

            $teamIds = $user->timeMembros()->pluck('time_id');

            if ($teamIds->isEmpty()) {
                $query->whereRaw('1 = 0');

                return;
            }

            $query->whereIn('time_id', $teamIds);
        });
    }

    public static function applyTarefaProjetoTimeFilter(Builder $builder): void
    {
        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();
        $currentTeamId = current_time_id();

        $builder->whereHas('tarefa.projeto', function (Builder $query) use ($user, $currentTeamId) {
            if ($currentTeamId !== null) {
                $query->where('time_id', $currentTeamId);

                return;
            }

            if (! $user instanceof User) {
                $query->whereRaw('1 = 0');

                return;
            }

            $teamIds = $user->timeMembros()->pluck('time_id');

            if ($teamIds->isEmpty()) {
                $query->whereRaw('1 = 0');

                return;
            }

            $query->whereIn('time_id', $teamIds);
        });
    }

    public static function applyMemberFilter(Builder $builder): void
    {
        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();
        $table = $builder->getModel()->getTable();

        if (! $user instanceof User) {
            if ($currentContaId = current_conta_id()) {
                $builder->where("{$table}.conta_id", $currentContaId);

                return;
            }

            $builder->whereRaw('1 = 0');

            return;
        }

        $builder->whereHas('membros', function (Builder $query) use ($user) {
            $query->where('usuario_id', $user->id);
        });

        if ($currentContaId = current_conta_id()) {
            $builder->where("{$table}.conta_id", $currentContaId);
        }
    }
}
