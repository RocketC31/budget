<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Repositories\TagRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    private TransactionRepository $transactionRepository;
    private TagRepository $tagRepository;

    public function __construct(TransactionRepository $transactionRepository, TagRepository $tagRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->tagRepository = $tagRepository;
    }

    public function index(): Response
    {
        return Inertia::render('Reports/Index');
    }

    private function weeklyReport($year): Response
    {
        return Inertia::render('Reports/WeeklyReport', [
            'year' => $year,
            'weeks' => $this->transactionRepository->getWeeklyBalance($year)
        ]);
    }

    private function mostExpensiveTags(): Response
    {
        $totalSpent = Transaction::ofSpace(session('space_id'))->where('type', 'spending')->sum('amount');
        $mostExpensiveTags = $this->tagRepository->getMostExpensiveTags(session('space_id'));

        return Inertia::render('Reports/MostExpensiveTags', compact('totalSpent', 'mostExpensiveTags'));
    }

    public function show(Request $request, $slug): string|Response
    {
        switch ($slug) {
            case 'weekly-report':
                $year = date('Y');

                if ($request->get('year')) {
                    $year = $request->get('year');
                }

                return $this->weeklyReport($year);

            case 'most-expensive-tags':
                return $this->mostExpensiveTags();

            default:
                return redirect('reports.index');
        }
    }
}
