<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Models\Earning;
use App\Models\Spending;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Recurring;
use App\Jobs\ProcessRecurrings;
use App\Repositories\RecurringRepository;
use Inertia\Inertia;
use Inertia\Response;
use Intervention\Image\Exception\NotFoundException;

class RecurringController extends Controller
{
    private RecurringRepository $recurringRepository;

    public function __construct(RecurringRepository $recurringRepository)
    {
        $this->recurringRepository = $recurringRepository;
    }

    public function index(): Response
    {
        $recurrings = Recurring::ofSpace(session('space_id'))->latest()->get();

        return Inertia::render('Recurrings/Index', ['recurrings' => $recurrings]);
    }

    public function edit($id): Response
    {
        $recurring = Recurring::ofSpace(session('space_id'))->find($id);
        if (!$recurring) {
            throw new NotFoundException();
        }

        return Inertia::render('Recurrings/Edit', compact('recurring'));
    }

    public function trash(): Response
    {
        return Inertia::render('Recurrings/Trash', [
            'recurrings' => Recurring::ofSpace(session("space_id"))->onlyTrashed()->get()
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate($this->recurringRepository->getValidationRules());

        $recurring = $this->recurringRepository->create(
            session('space_id'),
            $request->type,
            $request->interval,
            (int) ltrim($request->input('day'), 0),
            $request->start,
            $request->input('end', null),
            $request->input('tag', null),
            $request->input('description'),
            Helper::rawNumberToInteger($request->input('amount')),
            $request->currency_id
        );

        ProcessRecurrings::dispatch();

        return redirect()->route('transactions.index', ['recurring' => $recurring->id]);
    }

    public function update($id, Request $request): RedirectResponse
    {
        $recurring = Recurring::ofSpace(session('space_id'))->find($id);
        if (!$recurring) {
            throw new NotFoundException();
        }

        $request->validate([
            'date' => 'date|date_format:Y-m-d',
            'description' => 'required|max:255',
            'amount' => 'required|regex:/^\d*(\.\d{2})?$/',
        ]);
        $amount = Helper::rawNumberToInteger($request->input('amount'));
        $date = new \DateTime($request->input('date'));
        $this->recurringRepository->update($id, [
            'last_used_on' => $request->input('date'),
            'day' => $date->format('j'),
            'description' => $request->input('description'),
            'amount' => $amount
        ]);

        return redirect()->route('recurrings.index');
    }

    public function destroy($id): RedirectResponse
    {
        $recurring = Recurring::ofSpace(session('space_id'))->find($id);
        if (!$recurring) {
            throw new NotFoundException();
        }

        $recurring->delete();

        return back();
    }

    public function purge($id): RedirectResponse
    {
        $recurring = Recurring::ofSpace(session('space_id'))->onlyTrashed()->find($id);
        if (!$recurring) {
            throw new NotFoundException();
        }

        //Before remove, we need to unlink all earnings and spendings from this recurring
        Earning::withTrashed()->where('recurring_id', '=', $id)->update(['recurring_id' => null]);
        Spending::withTrashed()->where('recurring_id', '=', $id)->update(['recurring_id' => null]);

        $recurring->forceDelete();

        return back();
    }

    public function restore($id): RedirectResponse
    {
        $recurring = Recurring::ofSpace(session('space_id'))->onlyTrashed()->find($id);
        if (!$recurring) {
            throw new NotFoundException();
        }

        $recurring->restore();

        return back();
    }

    public function purgeAll(): RedirectResponse
    {
        //NO need check because if it's in trash, check already ok. And it's linked to space_id
        $recurringsIds = Recurring::ofSpace(session('space_id'))->onlyTrashed()->pluck('id')->toArray();

        //Unlink for recurring who will be purged
        Earning::withTrashed()->whereIn('recurring_id', $recurringsIds)->update(['recurring_id' => null]);
        Spending::withTrashed()->whereIn('recurring_id', $recurringsIds)->update(['recurring_id' => null]);

        //Then remove recurrings
        Recurring::ofSpace(session('space_id'))->onlyTrashed()->forceDelete();

        return back();
    }
}
