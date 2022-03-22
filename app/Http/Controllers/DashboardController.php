<?php

namespace App\Http\Controllers;

use App\Repositories\TagRepository;
use Illuminate\Http\Request;
use App\Repositories\DashboardRepository;
use Inertia\Inertia;

class DashboardController extends Controller
{
    private DashboardRepository $dashboardRepository;
    private TagRepository $tagRepository;

    public function __construct(DashboardRepository $dashboardRepository, TagRepository $tagRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
        $this->tagRepository = $tagRepository;
    }

    public function __invoke(Request $request)
    {
        $space_id = session('space_id');
        $currentYear = date('Y');
        $currentMonth = date('m');
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);

        $search = [
            "limit" => 3,
            "year" => $currentYear,
            "month" => $currentMonth
        ];
        $mostExpensiveTags = $this->tagRepository->getMostExpensiveTags($space_id, $search);

        return Inertia::render('Dashboard', [
            'month' => (int)date('n'),

            'widgets' => $request->user()->widgets(),

            'totalSpent' => $this->dashboardRepository->getTotalAmountSpent($currentYear, $currentMonth),
            'mostExpensiveTags' => $mostExpensiveTags,

            'daysInMonth' => $daysInMonth,
            'dailyBalance' => $this->dashboardRepository->getDailyBalance($currentYear, $currentMonth)
        ]);
    }
}
