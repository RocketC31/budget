<?php

namespace App\Http\Middleware;

use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        $user = $request->user();
        if (Auth::user()) {
            $user['spaces'] = Auth::user()->spaces()->get();
        }
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user
            ],
            'current_space' => session('space_id') ? Space::find(session('space_id')) : null
        ]);
    }
}
