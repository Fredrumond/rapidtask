<?php

namespace App\Policies;

use App\Models\TarefaComentario;
use App\Models\User;
use App\Policies\Concerns\HandlesTeamAuthorization;

class TarefaComentarioPolicy
{
    use HandlesTeamAuthorization;

    public function viewAny(?User $user): bool
    {
        return $this->isAuthenticatedMember($user);
    }

    public function view(?User $user, TarefaComentario $tarefaComentario): bool
    {
        return $this->canAccessTeam($user, $this->teamId($tarefaComentario));
    }

    public function create(?User $user): bool
    {
        return $this->canAccessCurrentTeam($user);
    }

    public function update(?User $user, TarefaComentario $tarefaComentario): bool
    {
        return $this->canAccessTeam($user, $this->teamId($tarefaComentario));
    }

    public function delete(?User $user, TarefaComentario $tarefaComentario): bool
    {
        return $this->canAccessTeam($user, $this->teamId($tarefaComentario));
    }

    protected function teamId(TarefaComentario $tarefaComentario): int
    {
        return $this->teamIdFromTarefaId((int) $tarefaComentario->tarefa_id);
    }
}
