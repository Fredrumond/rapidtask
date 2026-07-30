<?php

namespace App\Policies;

use App\Models\Cliente;
use App\Models\User;
use App\Policies\Concerns\HandlesTeamAuthorization;

class ClientePolicy
{
    use HandlesTeamAuthorization;

    public function viewAny(?User $user): bool
    {
        return $this->isAuthenticatedMember($user);
    }

    public function view(?User $user, Cliente $cliente): bool
    {
        return $this->canAccessTeam($user, $cliente->time_id);
    }

    public function create(?User $user): bool
    {
        return $this->canAccessCurrentTeam($user);
    }

    public function update(?User $user, Cliente $cliente): bool
    {
        return $this->canAccessTeam($user, $cliente->time_id);
    }

    public function delete(?User $user, Cliente $cliente): bool
    {
        return $this->canAccessTeam($user, $cliente->time_id);
    }
}
