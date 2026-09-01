<?php

namespace App\Policies;

use App\Models\ProjetoArquivo;
use App\Models\User;
use App\Policies\Concerns\HandlesTeamAuthorization;

class ProjetoArquivoPolicy
{
    use HandlesTeamAuthorization;

    public function viewAny(?User $user): bool
    {
        return $this->isAuthenticatedMember($user);
    }

    public function view(?User $user, ProjetoArquivo $projetoArquivo): bool
    {
        return $this->canAccessTeam($user, $this->teamId($projetoArquivo));
    }

    public function create(?User $user): bool
    {
        return $this->canAccessCurrentTeam($user);
    }

    public function update(?User $user, ProjetoArquivo $projetoArquivo): bool
    {
        return $this->canAccessTeam($user, $this->teamId($projetoArquivo));
    }

    public function delete(?User $user, ProjetoArquivo $projetoArquivo): bool
    {
        return $this->canAccessTeam($user, $this->teamId($projetoArquivo))
            && $this->isOwner($user, $projetoArquivo);
    }

    protected function isOwner(?User $user, ProjetoArquivo $projetoArquivo): bool
    {
        return $user !== null
            && (int) $projetoArquivo->usuario_id === (int) $user->id;
    }

    protected function teamId(ProjetoArquivo $projetoArquivo): int
    {
        return $this->teamIdFromProjetoId((int) $projetoArquivo->projeto_id);
    }
}
