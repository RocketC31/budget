<?php

namespace App\Widgets;

use App\Helper;
use App\Models\Space;
use App\Repositories\DashboardRepository;
use Inertia\Inertia;

class Balance
{
    private DashboardRepository$dashboardRepository;

    public function __construct()
    {
        $this->dashboardRepository = new DashboardRepository();
    }

    public function render()
    {
        $space = Space::find(session('space_id'));

        $currencySymbol = $space->currency->symbol;
        $balance = $this->dashboardRepository->getBalance(date('Y'), date('n'));

        return Inertia::render('Components/Widget/Balance', [
            'currencySymbol' => $currencySymbol,
            'balance' => Helper::formatNumber($balance / 100)
        ]);
    }
}
