<?php

namespace App\Http\Controllers\Auth;

use App\Actions\StoreSpaceInSessionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Repositories\LoginAttemptRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Nette\Schema\ValidationException;

class AuthenticatedSessionController extends Controller
{
    private LoginAttemptRepository $loginAttemptRepository;

    public function __construct(LoginAttemptRepository $loginAttemptRepository)
    {
        $this->loginAttemptRepository = $loginAttemptRepository;
    }

    /**
     * Display the login view.
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'demoMode' => config("app.demo_mode"),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param LoginRequest $request
     * @return RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();

            $user = Auth::user();
            $request->session()->regenerate();
            $this->loginAttemptRepository->create($user->id, $request->ip(), false);
            if (count($user->spaces) > 0) {
                (new StoreSpaceInSessionAction())->execute($user->spaces[0]->id);
            }

            return redirect()->intended(RouteServiceProvider::HOME);
        } catch (ValidationException $validationException) {
            if ($request->input('email')) {
                $user = User::where('email', $request->input('email'))->first();

                $this->loginAttemptRepository->create($user?->id, $request->ip(), true);
            }
            throw $validationException;
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
