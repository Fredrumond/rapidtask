<?php

namespace App\Policies;

use App\Models\Tarefa;
use App\Models\User;
use App\Policies\Concerns\HandlesTeamAuthorization;

class TarefaPolicy
{
    use HandlesTeamAuthorization;

    public function viewAny(?User $user): bool
    {
        return $this->isAuthenticatedMember($user);
    }

    public function view(?User $user, Tarefa $tarefa): bool
    {
        return $this->canAccessTeam($user, $this->teamId($tarefa));
    }

    public function create(?User $user): bool
    {
        return $this->canAccessCurrentTeam($user);
    }

    public function update(?User $user, Tarefa $tarefa): bool
    {
        return $this->canAccessTeam($user, $this->teamId($tarefa));
    }

    public function delete(?User $user, Tarefa $tarefa): bool
    {
        return $this->canAccessTeam($user, $this->teamId($tarefa));
    }

    protected function teamId(Tarefa $tarefa): int
    {
        return $this->teamIdFromProjetoId((int) $tarefa->projeto_id);
    }
}
