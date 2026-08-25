<?php

namespace App\Http\Controllers;

use App\Exceptions\ConviteDomainException;
use App\Exceptions\ConviteException;
use App\Models\TimeMembroConvite;
use App\Services\ConviteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ConviteController extends Controller
{
    public function aceitar(TimeMembroConvite $convite, ConviteService $service): RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        try {
            $service->aceitar((int) $convite->id, (int) $user->id);
        } catch (ConviteDomainException $exception) {
            abort($exception->isGone() ? 410 : 403, $exception->getMessage());
        } catch (ConviteException $exception) {
            abort(404, $exception->getMessage());
        }

        return redirect()
            ->route('times.show', $convite->time_id)
            ->with('status', 'Convite aceito.');
    }

    public function recusar(TimeMembroConvite $convite, ConviteService $service): RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        try {
            $service->recusar((int) $convite->id, (int) $user->id);
        } catch (ConviteDomainException $exception) {
            abort($exception->isGone() ? 410 : 403, $exception->getMessage());
        } catch (ConviteException $exception) {
            abort(404, $exception->getMessage());
        }

        return redirect()
            ->route('dashboard')
            ->with('status', 'Convite recusado.');
    }
}
