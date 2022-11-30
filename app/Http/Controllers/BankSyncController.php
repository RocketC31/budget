<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Space;
use App\Providers\NordigenServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class BankSyncController extends Controller
{
    public function __invoke(int $spaceId): RedirectResponse
    {
        // First we load spaceId, and check if we have right
        $space = Space::find($spaceId);
        //After we try to load BankModel
        $bank = Bank::ofSpace($space->id)->first();
        if (
            $space
            && Auth::user()->can('edit', $space)
            && $bank
            && Auth::user()->can('edit', $bank)
            && $bank->requisition_id
        ) {
            try {
                $bankProvider = new NordigenServiceProvider(
                    config('app.bank_sync.secret_id'),
                    config('app.bank_sync.secret_key')
                );
                $accounts = $bankProvider->getListOfAccounts($bank->requisition_id);
                if ($accounts) {
                    $bank->fill([
                        "account_id" => $accounts[0],
                        "link" => null,
                        "requisition_id" => null
                    ])->save();
                }
            } catch (\Exception $exception) {
            }
        }

        return redirect()->route('spaces.edit', $space->id);
    }
}
