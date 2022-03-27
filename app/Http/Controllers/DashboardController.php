<?php

namespace App\Http\Controllers;

use App\Repositories\TagRepository;
use Illuminate\Http\Request;
use App\Repositories\DashboardRepository;
use Illuminate\Support\Facades\Redis;
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

        $widgets = $request->user()->widgets();
        $balanceTick = null;
        $redisKey = "global_balance:start_month:space:" . $space_id;

        $balanceGlobalWidget = array_filter($widgets, function ($widget) {
            return $widget->type === "balance_global";
        });

        if (!empty($balanceGlobalWidget) && config("app.redis_available") && Redis::exists($redisKey)) {
            $balanceTick = (float)Redis::get($redisKey) * 100;
        }

        $mostExpensiveTags = $this->tagRepository->getMostExpensiveTags($space_id, $search);
        $dailyBalance = $this->dashboardRepository->getDailyBalance($currentYear, $currentMonth, $balanceTick);

        return Inertia::render('Dashboard', [
            'month' => (int)date('n'),

            'widgets' => $widgets,

            'totalSpent' => $this->dashboardRepository->getTotalAmountSpent($currentYear, $currentMonth),
            'mostExpensiveTags' => $mostExpensiveTags,

            'daysInMonth' => $daysInMonth,
            'dailyBalance' => $dailyBalance
        ]);
    }
}
