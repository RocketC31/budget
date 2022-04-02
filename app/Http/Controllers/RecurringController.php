<?php

namespace App\Http\Controllers;

use App\Helper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Recurring;
use App\Jobs\ProcessRecurrings;
use App\Models\Tag;
use App\Repositories\RecurringRepository;
use Inertia\Inertia;
use Inertia\Response;

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

    public function show(Recurring $recurring): Response
    {
        $this->authorize('view', $recurring);

        return Inertia::render('Recurrings/Show', compact('recurring'));
    }

    public function create(): Response
    {
        $tags = [];

        foreach (Tag::ofSpace(session('space_id'))->get() as $tag) {
            $tags[] = ['key' => $tag->id, 'label' => $tag->name];
        }

        return Inertia::render('Recurrings/Create', compact('tags'));
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
}
