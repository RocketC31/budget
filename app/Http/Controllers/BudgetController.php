<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Models\Tag;
use App\Repositories\BudgetRepository;
use App\Repositories\TagRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class BudgetController extends Controller
{
    private BudgetRepository $budgetRepository;
    private TagRepository $tagRepository;

    public function __construct(BudgetRepository $budgetRepository, TagRepository $tagRepository)
    {
        $this->budgetRepository = $budgetRepository;
        $this->tagRepository = $tagRepository;
    }

    public function index(): Response
    {
        return Inertia::render('Budgets/Index', [
            'budgets' => $this->budgetRepository->getActive()
        ]);
    }

    public function create()
    {
        $tags = Tag::ofSpace(session('space_id'))->latest()->get();

        return Inertia::render('Budgets/Create', ['tags' => $tags]);
    }

    public function store(Request $request)
    {
        $request->validate($this->budgetRepository->getValidationRules());

        $user = Auth::user();
        $tag = $this->tagRepository->getById($request->tag_id);

        if (!$user->can('view', $tag)) {
            throw ValidationException::withMessages(['tag_id' => __('validation.forbidden')]);
        }

        if ($this->budgetRepository->doesExist(session('space_id'), $request->tag_id)) {
            return redirect('/budgets/create')
                ->with('message', __('validation.budget_like_this_exist'));
        }

        $amount = Helper::rawNumberToInteger($request->amount);
        $this->budgetRepository->create(session('space_id'), $request->tag_id, $request->period, $amount);

        return redirect('/budgets');
    }
}
