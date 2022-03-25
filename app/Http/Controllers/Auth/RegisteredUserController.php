<?php

namespace App\Http\Controllers\Auth;

use App\Actions\CreateUserAction;
use App\Actions\SendVerificationMailAction;
use App\Actions\StoreSpaceInSessionAction;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Repositories\LoginAttemptRepository;
use App\Repositories\SpaceRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    private SpaceRepository $spaceRepository;
    private LoginAttemptRepository $loginAttemptRepository;

    public function __construct(
        SpaceRepository $spaceRepository,
        LoginAttemptRepository $loginAttemptRepository
    ) {
        $this->spaceRepository = $spaceRepository;
        $this->loginAttemptRepository = $loginAttemptRepository;
    }

    /**
     * Display the registration view.
     *
     * @return Response
     */
    public function create(): Response
    {
        if (config('app.disable_registration')) {
            abort(404);
        }

        return Inertia::render('Auth/Register', [
            'currencies' => Currency::orderBy('name')->get()
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     */
    public function store(Request $request)
    {
        if (config('app.disable_registration')) {
            abort(404);
        }

        $request->validate(User::getValidationRulesForRegistration());
        $user = (new CreateUserAction())->execute($request->name, $request->email, $request->password);
        $space = $this->spaceRepository->create($request->currency, $user->name . '\'s Space');
        $user->spaces()->attach($space->id, ['role' => 'admin']);

        event(new Registered($user));

        Auth::login($user);

        $this->loginAttemptRepository->create($user->id, $request->ip(), false);

        (new StoreSpaceInSessionAction())->execute($user->spaces[0]->id);

        (new SendVerificationMailAction())->execute($user->id);

        return redirect(RouteServiceProvider::HOME);
    }
}
