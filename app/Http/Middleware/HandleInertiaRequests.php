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
        $spaces = $request->user();
        if (Auth::user()) {
            $spaces = Auth::user()->spaces()->get();
        }
        $space = session('space_id') ? Space::find(session('space_id')) : null;
        $versionFileExists = file_exists(base_path() . '/version.txt');
        $versionNumber = $versionFileExists ? file_get_contents(base_path() . '/version.txt') : '-';
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user()
            ],
            'spaces' => $spaces,
            'current_space' => $space,
            'currency' => $space?->currency->symbol,
            'versionNumber' => $versionNumber,
            'patchMethodAvailable' => config("app.patch_method_available"),
            'flash' => [
                'message' => fn () => $request->session()->get('message')
            ],
            'registrationDisable' => config('app.disable_registration'),
            'bank_sync_available' => config('app.bank_sync.available'),
        ]);
    }
}
