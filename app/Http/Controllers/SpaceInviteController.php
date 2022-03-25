<?php

namespace App\Http\Controllers;

use App\Actions\AcceptSpaceInviteAction;
use App\Actions\DenySpaceInviteAction;
use App\Models\Space;
use App\Models\SpaceInvite;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SpaceInviteController extends Controller
{
    public function show(Space $space, SpaceInvite $invite)
    {
        if ($invite->space->id !== $space->id) {
            abort(404);
        }

        $this->authorize('access', $invite);

        if ($invite->accepted !== null) {
            abort(410);
        }

        $invite->load('space');
        $invite->load('invitee');
        $invite->load('inviter');

        return Inertia::render('SpacesInvite/Show', [
            'invite' => $invite
        ]);
    }

    public function accept(Request $request, Space $space, SpaceInvite $invite)
    {
        if ($invite->space->id !== $space->id) {
            abort(404);
        }

        $this->authorize('access', $invite);

        if ($invite->accepted !== null) {
            abort(410);
        }

        if (!$request->user()->can('accept', SpaceInvite::class)) {
            $request->session()->flash('maximum_reached', true);

            return redirect()->route('space_invites.show', [
                'space' => $space->id,
                'invite' => $invite->id
            ]);
        }

        (new AcceptSpaceInviteAction())->execute($invite->id);

        return redirect()->route('settings.spaces.index');
    }

    public function deny(Request $request, Space $space, SpaceInvite $invite)
    {
        if ($invite->space->id !== $space->id) {
            abort(404);
        }

        $this->authorize('access', $invite);

        if ($invite->accepted !== null) {
            abort(410);
        }

        (new DenySpaceInviteAction())->execute($invite->id);

        return redirect()->route('settings.spaces.index');
    }
}
