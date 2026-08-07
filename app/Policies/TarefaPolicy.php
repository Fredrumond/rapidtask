<?php

namespace App\Policies;

use App\Models\Conta;
use App\Models\Tarefa;
use App\Models\User;
use App\Policies\Concerns\HandlesTeamAuthorization;

class TarefaPolicy
{
    use HandlesTeamAuthorization;

    public function viewAny(Conta|User|null $actor): bool
    {
        if ($actor instanceof Conta) {
            return $this->canAccessCurrentTeam($actor);
        }

        return $this->isAuthenticatedMember($actor);
    }

    public function view(Conta|User|null $actor, Tarefa $tarefa): bool
    {
        return $this->canAccessTeam($actor, $this->teamId($tarefa));
    }

    public function create(Conta|User|null $actor): bool
    {
        return $this->canAccessCurrentTeam($actor);
    }

    public function update(Conta|User|null $actor, Tarefa $tarefa): bool
    {
        return $this->canAccessTeam($actor, $this->teamId($tarefa));
    }

    public function delete(Conta|User|null $actor, Tarefa $tarefa): bool
    {
        return $this->canAccessTeam($actor, $this->teamId($tarefa));
    }

    protected function teamId(Tarefa $tarefa): int
    {
        return $this->teamIdFromProjetoId((int) $tarefa->projeto_id);
    }
}
