<?php

namespace App\Http\Middleware;

use App\Support\CurrentTeam;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentTeam
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && current_time_id() === null) {
            $firstTeam = $request->user()->times()->first();

            if ($firstTeam !== null) {
                CurrentTeam::set($firstTeam->id);
            }
        }

        return $next($request);
    }
}
