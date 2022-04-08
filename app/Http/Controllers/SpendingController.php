<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Models\Recurring;
use App\Models\Space;
use App\Models\Spending;
use App\Models\Tag;
use App\Repositories\ConversionRateRepository;
use App\Repositories\RecurringRepository;
use App\Repositories\SpendingRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SpendingController extends Controller
{
    private SpendingRepository $spendingRepository;
    private ConversionRateRepository $conversionRateRepository;
    private RecurringRepository $recurringRepository;

    public function __construct(
        SpendingRepository $spendingRepository,
        ConversionRateRepository $conversionRateRepository,
        RecurringRepository $recurringRepository
    ) {
        $this->spendingRepository = $spendingRepository;
        $this->conversionRateRepository = $conversionRateRepository;
        $this->recurringRepository = $recurringRepository;
    }

    public function show(Spending $spending): Response
    {
        $this->authorize('view', $spending);
        $spending->load('attachments');
        return Inertia::render('Spendings/Show', [
            'spending' => $spending
        ]);
    }

    public function store(Request $request)
    {
        $request->validate($this->spendingRepository->getValidationRules());

        $amount = Helper::rawNumberToInteger($request->input('amount'));

        // Convert amount if a different currency was selected
        if ($request->has('currency_id') && $request->currency_id !== Space::find(session('space_id'))->currency_id) {
            $amount = $this->conversionRateRepository->convert(
                $request->currency_id,
                Space::find(session('space_id'))->currency_id,
                $amount
            );
        }

        $this->spendingRepository->create(
            session('space_id'),
            null,
            null,
            $request->input('tag_id'),
            $request->input('date'),
            $request->input('description'),
            $amount
        );

        return redirect()->route('transactions.index');
    }

    public function edit(Spending $spending): Response
    {
        $this->authorize('edit', $spending);

        $tags = Tag::ofSpace(session('space_id'))->latest()->get();

        return Inertia::render('Spendings/Edit', compact('tags', 'spending'));
    }

    public function update(Request $request, Spending $spending)
    {
        $this->authorize('update', $spending);

        $request->validate($this->spendingRepository->getValidationRules());

        $amount = Helper::rawNumberToInteger($request->input('amount'));

        $this->spendingRepository->update($spending->id, [
            'tag_id' => $request->input('tag_id'),
            'happened_on' => $request->input('date'),
            'description' => $request->input('description'),
            'amount' => $amount
        ]);

        if ($request->query->get("recurring_update") && $spending->recurring_id) {
            $this->recurringRepository->update($spending->recurring_id, [
                'starts_on' => $request->input('date'),
                'last_used_on' => $request->input('date'),
                'tag_id' => $request->input('tag_id'),
                'description' => $request->input('description'),
                'amount' => $amount
            ]);
        }

        return redirect()->route('transactions.index');
    }

    public function destroy($id, Request $request): RedirectResponse
    {
        $spending = Spending::find($id);
        $this->authorize('delete', $spending);

        if ($request->query->get("recurring_remove") && $spending->recurring_id) {
            $recurring = Recurring::find($spending->recurring_id);
            $recurring->delete();
        }

        $spending->delete();

        return redirect()
            ->back();
    }

    public function restore($id)
    {
        $spending = Spending::withTrashed()->find($id);

        if (!$spending) {
            // 404
        }

        $this->authorize('restore', $spending);

        $spending->restore();

        return redirect()->route('transactions.index');
    }
}
