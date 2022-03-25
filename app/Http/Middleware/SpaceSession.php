<?php

namespace App\Http\Middleware;

use App\Actions\StoreSpaceInSessionAction;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpaceSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && is_null(session("space_id"))) {
            if (count(Auth::user()->spaces) > 0) {
                (new StoreSpaceInSessionAction())->execute(Auth::user()->spaces[0]->id);
            }
        }

        return $next($request);
    }
}
