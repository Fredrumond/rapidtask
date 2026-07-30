<?php

namespace App\Policies;

use App\Models\Time;
use App\Models\User;
use App\Policies\Concerns\HandlesTeamAuthorization;

class TimePolicy
{
    use HandlesTeamAuthorization;

    public function viewAny(?User $user): bool
    {
        return $user !== null;
    }

    public function view(?User $user, Time $time): bool
    {
        return $this->canAccessTeam($user, $time->id);
    }

    public function create(?User $user): bool
    {
        return $user !== null;
    }

    public function update(?User $user, Time $time): bool
    {
        return $this->isTeamAdmin($user, $time->id);
    }

    public function delete(?User $user, Time $time): bool
    {
        return $this->isTeamAdmin($user, $time->id);
    }
}
