<?php

namespace App\Policies;

use App\Models\Conta;
use App\Models\ProjetoArquivo;
use App\Models\User;
use App\Policies\Concerns\HandlesTeamAuthorization;

class ProjetoArquivoPolicy
{
    use HandlesTeamAuthorization;

    public function viewAny(Conta|User|null $actor): bool
    {
        if ($actor instanceof Conta) {
            return $this->canAccessCurrentTeam($actor);
        }

        return $this->isAuthenticatedMember($actor);
    }

    public function view(Conta|User|null $actor, ProjetoArquivo $projetoArquivo): bool
    {
        return $this->canAccessTeam($actor, $this->teamId($projetoArquivo));
    }

    public function create(Conta|User|null $actor): bool
    {
        return $this->canAccessCurrentTeam($actor);
    }

    public function update(Conta|User|null $actor, ProjetoArquivo $projetoArquivo): bool
    {
        return $this->canAccessTeam($actor, $this->teamId($projetoArquivo));
    }

    public function delete(Conta|User|null $actor, ProjetoArquivo $projetoArquivo): bool
    {
        return $this->canAccessTeam($actor, $this->teamId($projetoArquivo))
            && $this->isOwner($actor, $projetoArquivo);
    }

    protected function isOwner(Conta|User|null $actor, ProjetoArquivo $projetoArquivo): bool
    {
        if ($actor instanceof Conta) {
            return (int) $projetoArquivo->usuario_id === (int) $actor->usuario_id;
        }

        if ($actor instanceof User) {
            return (int) $projetoArquivo->usuario_id === (int) $actor->id;
        }

        return false;
    }

    protected function teamId(ProjetoArquivo $projetoArquivo): int
    {
        return $this->teamIdFromProjetoId((int) $projetoArquivo->projeto_id);
    }
}
