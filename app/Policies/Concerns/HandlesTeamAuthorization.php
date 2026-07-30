<?php

namespace App\Policies\Concerns;

use App\Models\Projeto;
use App\Models\Tarefa;
use App\Models\User;

trait HandlesTeamAuthorization
{
    protected function isAuthenticatedMember(?User $user): bool
    {
        return $user !== null && $user->times()->exists();
    }

    protected function canAccessTeam(?User $user, int $timeId): bool
    {
        return $user !== null && $user->belongsToTime($timeId);
    }

    protected function isTeamAdmin(?User $user, int $timeId): bool
    {
        return $user !== null && $user->isAdminOf($timeId);
    }

    protected function canAccessCurrentTeam(?User $user): bool
    {
        $teamId = current_time_id();

        return $teamId !== null && $this->canAccessTeam($user, $teamId);
    }

    protected function teamIdFromProjetoId(int $projetoId): int
    {
        return (int) Projeto::query()
            ->withoutGlobalScope('team')
            ->whereKey($projetoId)
            ->value('time_id');
    }

    protected function teamIdFromTarefaId(int $tarefaId): int
    {
        $projetoId = Tarefa::query()
            ->withoutGlobalScope('team')
            ->whereKey($tarefaId)
            ->value('projeto_id');

        return $this->teamIdFromProjetoId((int) $projetoId);
    }
}
