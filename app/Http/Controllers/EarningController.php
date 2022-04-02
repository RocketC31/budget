<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Models\Recurring;
use App\Repositories\RecurringRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Earning;
use App\Models\Space;
use App\Repositories\ConversionRateRepository;
use App\Repositories\EarningRepository;
use Inertia\Inertia;
use Inertia\Response;

class EarningController extends Controller
{
    private EarningRepository $earningRepository;
    private ConversionRateRepository $conversionRateRepository;
    private RecurringRepository $recurringRepository;

    public function __construct(
        EarningRepository $earningRepository,
        ConversionRateRepository $conversionRateRepository,
        RecurringRepository $recurringRepository
    ) {
        $this->earningRepository = $earningRepository;
        $this->conversionRateRepository = $conversionRateRepository;
        $this->recurringRepository = $recurringRepository;
    }

    public function show(Earning $earning): Response
    {
        $this->authorize('view', $earning);

        return Inertia::render('Earnings/Show', [
            'earning' => $earning
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate($this->earningRepository->getValidationRules());

        $amount = Helper::rawNumberToInteger($request->input('amount'));

        // Convert amount if a different currency was selected
        if ($request->has('currency_id') && $request->currency_id !== Space::find(session('space_id'))->currency_id) {
            $amount = $this->conversionRateRepository->convert(
                $request->currency_id,
                Space::find(session('space_id'))->currency_id,
                $amount
            );
        }

        $this->earningRepository->create(
            session('space_id'),
            null,
            $request->input('date'),
            $request->input('description'),
            $amount
        );

        return redirect()->route('transactions.index');
    }

    public function edit(Earning $earning)
    {
        $this->authorize('edit', $earning);

        return Inertia::render('Earnings/Edit', compact('earning'));
    }

    public function update(Request $request, Earning $earning)
    {
        $this->authorize('update', $earning);

        $request->validate($this->earningRepository->getValidationRules());

        $this->earningRepository->update($earning->id, [
            'happened_on' => $request->input('date'),
            'description' => $request->input('description'),
            'amount' => Helper::rawNumberToInteger($request->input('amount'))
        ]);

        if ($request->query->get("recurring_update") && $earning->recurring_id) {
            $this->recurringRepository->update($earning->recurring_id, [
                'starts_on' => $request->input('date'),
                'last_used_on' => $request->input('date'),
            ]);
        }

        return redirect()->route('transactions.index');
    }

    public function destroy(Earning $earning, Request $request): RedirectResponse
    {
        $this->authorize('delete', $earning);

        if ($request->query->get("recurring_remove") && $earning->recurring_id) {
            $recurring = Recurring::find($earning->recurring_id);
            $recurring->delete();
        }

        $earning->delete();

        return redirect()
            ->back();
    }

    public function restore($id): RedirectResponse
    {
        $earning = Earning::withTrashed()->find($id);

        if (!$earning) {
            // 404
        }

        $this->authorize('restore', $earning);

        $earning->restore();

        return redirect()->route('transactions.index');
    }
}
