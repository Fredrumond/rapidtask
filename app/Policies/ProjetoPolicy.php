<?php

namespace App\Policies;

use App\Models\Projeto;
use App\Models\User;
use App\Policies\Concerns\HandlesTeamAuthorization;

class ProjetoPolicy
{
    use HandlesTeamAuthorization;

    public function viewAny(?User $user): bool
    {
        return $this->isAuthenticatedMember($user);
    }

    public function view(?User $user, Projeto $projeto): bool
    {
        return $this->canAccessTeam($user, $projeto->time_id);
    }

    public function create(?User $user): bool
    {
        return $this->canAccessCurrentTeam($user);
    }

    public function update(?User $user, Projeto $projeto): bool
    {
        return $this->canAccessTeam($user, $projeto->time_id);
    }

    public function delete(?User $user, Projeto $projeto): bool
    {
        return $this->canAccessTeam($user, $projeto->time_id);
    }
}
