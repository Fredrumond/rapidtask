<?php

namespace App\Policies;

use App\Models\Conta;
use App\Models\Projeto;
use App\Models\User;
use App\Policies\Concerns\HandlesTeamAuthorization;

class ProjetoPolicy
{
    use HandlesTeamAuthorization;

    public function viewAny(Conta|User|null $actor): bool
    {
        if ($actor instanceof Conta) {
            return $this->canAccessCurrentTeam($actor);
        }

        return $this->isAuthenticatedMember($actor);
    }

    public function view(Conta|User|null $actor, Projeto $projeto): bool
    {
        return $this->canAccessTeam($actor, (int) $projeto->time_id);
    }

    public function create(Conta|User|null $actor): bool
    {
        return $this->canAccessCurrentTeam($actor);
    }

    public function update(Conta|User|null $actor, Projeto $projeto): bool
    {
        return $this->canAccessTeam($actor, (int) $projeto->time_id);
    }

    public function delete(Conta|User|null $actor, Projeto $projeto): bool
    {
        return $this->canAccessTeam($actor, (int) $projeto->time_id);
    }
}
