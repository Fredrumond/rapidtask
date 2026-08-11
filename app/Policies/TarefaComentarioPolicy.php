<?php

namespace App\Policies;

use App\Models\Conta;
use App\Models\TarefaComentario;
use App\Models\User;
use App\Policies\Concerns\HandlesTeamAuthorization;

class TarefaComentarioPolicy
{
    use HandlesTeamAuthorization;

    public function viewAny(Conta|User|null $actor): bool
    {
        if ($actor instanceof Conta) {
            return $this->canAccessCurrentTeam($actor);
        }

        return $this->isAuthenticatedMember($actor);
    }

    public function view(Conta|User|null $actor, TarefaComentario $tarefaComentario): bool
    {
        return $this->canAccessTeam($actor, $this->teamId($tarefaComentario));
    }

    public function create(Conta|User|null $actor): bool
    {
        return $this->canAccessCurrentTeam($actor);
    }

    public function update(Conta|User|null $actor, TarefaComentario $tarefaComentario): bool
    {
        return $this->canAccessTeam($actor, $this->teamId($tarefaComentario))
            && $this->isAuthor($actor, $tarefaComentario);
    }

    public function delete(Conta|User|null $actor, TarefaComentario $tarefaComentario): bool
    {
        return $this->canAccessTeam($actor, $this->teamId($tarefaComentario))
            && $this->isAuthor($actor, $tarefaComentario);
    }

    protected function isAuthor(Conta|User|null $actor, TarefaComentario $tarefaComentario): bool
    {
        if ($actor instanceof Conta) {
            return (int) $tarefaComentario->usuario_id === (int) $actor->usuario_id;
        }

        if ($actor instanceof User) {
            return (int) $tarefaComentario->usuario_id === (int) $actor->id;
        }

        return false;
    }

    protected function teamId(TarefaComentario $tarefaComentario): int
    {
        return $this->teamIdFromTarefaId((int) $tarefaComentario->tarefa_id);
    }
}
