<?php

namespace App\Widgets;

use App\Helper;
use App\Models\Widget;
use App\Repositories\DashboardRepository;

class Balance extends Widget
{
    private DashboardRepository $dashboardRepository;

    public function __construct()
    {
        parent::__construct();
        $this->dashboardRepository = new DashboardRepository();
        $this->appends = array_merge($this->appends, ['balance']);
    }

    /**
     * @return string
     */
    public function getBalanceAttribute(): string
    {
        $balance = $this->dashboardRepository->getBalance(date('Y'), date('n'));
        return Helper::formatNumber($balance / 100);
    }
}
