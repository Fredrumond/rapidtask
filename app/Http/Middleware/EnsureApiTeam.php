<?php

namespace App\Http\Middleware;

use App\Enums\HttpCode;
use App\Models\Conta;
use App\Support\CurrentTeam;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiTeam
{
    /**
     * Resolve time_id from query (GET) or body (mutations).
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $rawTimeId = $request->input('time_id');

            if ($rawTimeId === null || $rawTimeId === '') {
                return response()->json([
                    'message' => 'O time_id é obrigatório.',
                    'data' => [],
                ], HttpCode::BAD_REQUEST->value);
            }

            if (! ctype_digit((string) $rawTimeId) || (int) $rawTimeId < 1) {
                return response()->json([
                    'message' => 'O time_id é inválido.',
                    'data' => [],
                ], HttpCode::BAD_REQUEST->value);
            }

            $timeId = (int) $rawTimeId;
            $conta = $request->user();

            if (! $conta instanceof Conta || ! $conta->ownsTime($timeId)) {
                return response()->json([
                    'message' => 'O time informado não pertence à conta autenticada.',
                    'data' => [],
                ], HttpCode::FORBIDDEN->value);
            }

            CurrentTeam::setForRequest($timeId);

            return $next($request);
        } finally {
            CurrentTeam::clearRequestOverride();
        }
    }
}
