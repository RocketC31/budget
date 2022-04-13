<?php

namespace App\Http\Controllers;

use App\Actions\CreateSpaceAction;
use App\Actions\CreateSpaceInviteAction;
use App\Actions\StoreSpaceInSessionAction;
use App\Exceptions\SpaceInviteAlreadyExistsException;
use App\Exceptions\SpaceInviteInviteeAlreadyPresentException;
use App\Mail\InvitedToSpace;
use App\Models\Currency;
use App\Models\Space;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class SpaceController extends Controller
{
    public function create(): Response
    {
        $currencies = Currency::all();

        return Inertia::render('Spaces/Create', ['currencies' => $currencies]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|max:255',
            'currency_id' => 'required|exists:currencies,id'
        ]);

        if (!$request->user()->can('create', Space::class)) {
            $request->session()->flash('maximum_reached', true);

            return redirect()->route('spaces.create');
        }

        (new CreateSpaceAction())->execute($request->name, $request->currency_id, Auth::id());

        return redirect()->route('settings.spaces.index');
    }

    public function show($id): RedirectResponse
    {
        $space = Space::find($id);

        if ($space->users->contains(Auth::user()->id)) {
            (new StoreSpaceInSessionAction())->execute($space->id);
        }

        return redirect()->route('dashboard');
    }

    public function edit(Space $space): Response|RedirectResponse
    {
        if (Auth::user()->cant('edit', $space)) {
            return redirect()->route('settings.spaces.index');
        }

        $space->load('currency');
        $space->load('invites');
        foreach ($space->invites as $invite) {
            $invite->load('inviter');
            $invite->load('invitee');
        }
        $space->load('users');

        return Inertia::render('Spaces/Edit', ['space' => $space]);
    }

    public function update(Request $request, Space $space): RedirectResponse
    {
        if (Auth::user()->cant('edit', $space)) {
            return redirect()->route('settings.spaces.index');
        }

        $request->validate([
            'name' => 'required|max:255'
        ]);

        $space->fill([
            'name' => $request->name
        ])->save();

        return back();
    }

    public function invite(Request $request, Space $space): RedirectResponse
    {
        $authenticatedUser = Auth::user();

        if ($authenticatedUser->cant('edit', $space)) {
            return redirect()->route('settings.spaces.index');
        }

        $request->validate([
            'email' => 'required|exists:users,email',
            'role' => 'required|in:admin,regular'
        ]);

        $inviteeUser = User::where('email', $request->email)->first();

        try {
            $invite = (new CreateSpaceInviteAction())->execute(
                $space->id,
                $inviteeUser->id,
                $authenticatedUser->id,
                $request->role
            );
        } catch (SpaceInviteInviteeAlreadyPresentException $e) {
            $request->session()->flash('inviteStatus', 'present');
            return redirect()
                ->route('spaces.edit', ['space' => $space->id]);
        } catch (SpaceInviteAlreadyExistsException $e) {
            $request->session()->flash('inviteStatus', 'exists');
            return redirect()
                ->route('spaces.edit', ['space' => $space->id]);
        }

        try {
            Mail::to($inviteeUser->email)->send(new InvitedToSpace($invite, $inviteeUser->language));
        } catch (\Exception $e) {
        }
        $request->session()->flash('inviteStatus', 'success');
        return redirect()
            ->route('spaces.edit', ['space' => $space->id]);
    }
}
