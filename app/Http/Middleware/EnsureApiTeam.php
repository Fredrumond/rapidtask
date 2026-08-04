<?php

namespace App\Http\Middleware;

use App\Enums\HttpCode;
use App\Support\CurrentTeam;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiTeam
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $rawTimeId = $request->header(CurrentTeam::HEADER_NAME);

            if ($rawTimeId === null || $rawTimeId === '') {
                return response()->json([
                    'message' => 'O header X-Time-Id é obrigatório.',
                    'data' => [],
                ], HttpCode::BAD_REQUEST->value);
            }

            if (! ctype_digit((string) $rawTimeId) || (int) $rawTimeId < 1) {
                return response()->json([
                    'message' => 'O header X-Time-Id é inválido.',
                    'data' => [],
                ], HttpCode::BAD_REQUEST->value);
            }

            $timeId = (int) $rawTimeId;
            $user = $request->user();

            if ($user === null || ! $user->belongsToTime($timeId)) {
                return response()->json([
                    'message' => 'Você não pertence ao time informado.',
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
