<?php

namespace App\Policies;

use App\Models\Conta;
use App\Models\ProjetoAnotacao;
use App\Models\User;
use App\Policies\Concerns\HandlesTeamAuthorization;

class ProjetoAnotacaoPolicy
{
    use HandlesTeamAuthorization;

    public function viewAny(Conta|User|null $actor): bool
    {
        if ($actor instanceof Conta) {
            return $this->canAccessCurrentTeam($actor);
        }

        return $this->isAuthenticatedMember($actor);
    }

    public function view(Conta|User|null $actor, ProjetoAnotacao $projetoAnotacao): bool
    {
        return $this->canAccessTeam($actor, $this->teamId($projetoAnotacao));
    }

    public function create(Conta|User|null $actor): bool
    {
        return $this->canAccessCurrentTeam($actor);
    }

    public function update(Conta|User|null $actor, ProjetoAnotacao $projetoAnotacao): bool
    {
        return $this->canAccessTeam($actor, $this->teamId($projetoAnotacao))
            && $this->isAuthor($actor, $projetoAnotacao);
    }

    public function delete(Conta|User|null $actor, ProjetoAnotacao $projetoAnotacao): bool
    {
        return $this->canAccessTeam($actor, $this->teamId($projetoAnotacao))
            && $this->isAuthor($actor, $projetoAnotacao);
    }

    protected function isAuthor(Conta|User|null $actor, ProjetoAnotacao $projetoAnotacao): bool
    {
        if ($actor instanceof Conta) {
            return (int) $projetoAnotacao->usuario_id === (int) $actor->usuario_id;
        }

        if ($actor instanceof User) {
            return (int) $projetoAnotacao->usuario_id === (int) $actor->id;
        }

        return false;
    }

    protected function teamId(ProjetoAnotacao $projetoAnotacao): int
    {
        return $this->teamIdFromProjetoId((int) $projetoAnotacao->projeto_id);
    }
}
