<?php

namespace App\Policies;

use App\Models\ProjetoAnotacao;
use App\Models\User;
use App\Policies\Concerns\HandlesTeamAuthorization;

class ProjetoAnotacaoPolicy
{
    use HandlesTeamAuthorization;

    public function viewAny(?User $user): bool
    {
        return $this->isAuthenticatedMember($user);
    }

    public function view(?User $user, ProjetoAnotacao $projetoAnotacao): bool
    {
        return $this->canAccessTeam($user, $this->teamId($projetoAnotacao));
    }

    public function create(?User $user): bool
    {
        return $this->canAccessCurrentTeam($user);
    }

    public function update(?User $user, ProjetoAnotacao $projetoAnotacao): bool
    {
        return $this->canAccessTeam($user, $this->teamId($projetoAnotacao));
    }

    public function delete(?User $user, ProjetoAnotacao $projetoAnotacao): bool
    {
        return $this->canAccessTeam($user, $this->teamId($projetoAnotacao));
    }

    protected function teamId(ProjetoAnotacao $projetoAnotacao): int
    {
        return $this->teamIdFromProjetoId((int) $projetoAnotacao->projeto_id);
    }
}
