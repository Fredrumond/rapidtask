<?php

namespace App\Http\Controllers;

use App\Models\Time;
use App\Models\TimeMembro;
use App\Models\TimeMembroConvite;
use App\Support\CurrentTeam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ConviteController extends Controller
{
    public function aceitar(TimeMembroConvite $convite): RedirectResponse
    {
        abort_unless($convite->status === 0, 410, 'Convite já utilizado.');

        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        abort_unless(
            strcasecmp($user->email, $convite->email) === 0,
            403,
            'Este convite é para outro e-mail.'
        );

        $contaId = Time::withoutGlobalScopes()
            ->whereKey($convite->time_id)
            ->value('conta_id');

        abort_if(
            $contaId !== null && $user->belongsToOtherConta((int) $contaId),
            403,
            'Você já pertence a outra conta na plataforma.'
        );

        TimeMembro::query()->firstOrCreate(
            [
                'time_id' => $convite->time_id,
                'usuario_id' => $user->id,
            ],
            [
                'nivel_id' => 2,
            ]
        );

        $convite->update(['status' => 1]);

        CurrentTeam::set($convite->time_id);

        return redirect()
            ->route('times.show', $convite->time_id)
            ->with('status', 'Convite aceito.');
    }

    public function recusar(TimeMembroConvite $convite): RedirectResponse
    {
        abort_unless($convite->status === 0, 410);

        $user = Auth::user();
        abort_unless($user && strcasecmp($user->email, $convite->email) === 0, 403);

        $convite->update(['status' => 2]);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Convite recusado.');
    }
}
