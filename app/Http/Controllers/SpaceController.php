<?php

namespace App\Http\Controllers;

use App\Actions\CreateSpaceAction;
use App\Actions\CreateSpaceInviteAction;
use App\Actions\StoreSpaceInSessionAction;
use App\Exceptions\SpaceInviteAlreadyExistsException;
use App\Exceptions\SpaceInviteInviteeAlreadyPresentException;
use App\Mail\InvitedToSpace;
use App\Models\Bank;
use App\Models\Currency;
use App\Models\Space;
use App\Models\UserSpace;
use App\Models\User;
use App\Providers\NordigenServiceProvider;
use App\Repositories\BankRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class SpaceController extends Controller
{
    private BankRepository $bankRepository;

    public function __construct(
        BankRepository $bankRepository
    ) {
        $this->bankRepository = $bankRepository;
    }

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
        $banks = [];
        //If active sync is send, and if we have config available
        $space->load('bank');
        if ($space->sync_active && config('app.bank_sync.available') && !$space->bank) {
            try {
                $bankProvider = new NordigenServiceProvider(
                    config('app.bank_sync.secret_id'),
                    config('app.bank_sync.secret_key')
                );

                $banks = $bankProvider->getListOfInstitutions(Auth::user()->language);
            } catch (\Exception $exception) {
            }
        }

        return Inertia::render('Spaces/Edit', ['space' => $space, 'banks' => $banks]);
    }

    public function update(Request $request, Space $space): RedirectResponse
    {
        if (Auth::user()->cant('edit', $space)) {
            return redirect()->route('settings.spaces.index');
        }

        $request->validate([
            'name' => 'required|max:255',
            'sync_active' => 'integer|in:1,0',
            'bank' => ''
        ]);

        $data = [
            'name' => $request->name
        ];

        //Remove part of bank data
        $space->load('bank');
        if (!$request->sync_active && $space->bank) {
            $space->bank->delete();
        }

        //If active sync is send, and if we have config available
        if (config('app.bank_sync.available')) {
            $data['sync_active'] = $request->sync_active ? 1 : 0;

            if ($request->bank && $request->sync_active && (!$space->bank || is_null($space->bank->account_id))) {
                $request->validate($this->bankRepository->getValidationRules());
                //If whe have data for bank
                try {
                    $bankProvider = new NordigenServiceProvider(
                        config('app.bank_sync.secret_id'),
                        config('app.bank_sync.secret_key')
                    );

                    $sessionData = $bankProvider->getSessionData(route('sync_bank', $space->id), $request->bank['id']);
                    if (array_key_exists("link", $sessionData) && array_key_exists("requisition_id", $sessionData)) {
                        Bank::updateOrCreate([
                            'space_id' => $space->id
                        ], [
                            'requisition_id' => $sessionData["requisition_id"],
                            'name' => $request->bank["name"],
                            'logo' => $request->bank["logo"],
                            'link' => $sessionData["link"]
                        ]);
                    }
                } catch (\Exception $exception) {
                }
            }
        }

        $space->fill($data)->save();

        return back();
    }

    public function delete(Request $request, Space $space): RedirectResponse
    {
        $this->authorize("delete", $space);

        //Need to have one other space. If not, you cant delete !
        $usersSpaceCount = UserSpace::where('user_id', '=', Auth::user()->id)->where('role', '=', 'admin')->count();
        if ($usersSpaceCount <= 1) {
            abort(403, "You have only one last space. You can't delete it !");
        }

        //TODO : remove all one by one where space_id exist in database
        // Or... Add foreign key constrains on delete cascade
        $space->forceDelete();

        $newSpaceForSession = Auth::user()->spaces
            ->where('id', $space->id)
            ->first();

        (new StoreSpaceInSessionAction())->execute($newSpaceForSession->id);

        return redirect()->route('dashboard');
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
