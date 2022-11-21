<?php

namespace App\Repositories;

use App\Helper;
use App\Models\Space;
use App\Models\Transaction;

class DashboardRepository
{
    public function getBalance(string $year, string $month)
    {
        return Space::find(session('space_id'))->monthlyBalance($year, $month);
    }

    public function getBalanceGlobal(int $spaceId, ?\DateTime $endDate = null)
    {
        return Space::find($spaceId)->balanceGlobal($endDate);
    }

    public function getRecurrings(string $year, string $month)
    {
        return Space::find(session('space_id'))->monthlyRecurrings($year, $month);
    }

    public function getLeftToSpend(string $year, string $month)
    {
        $balance = $this->getBalance($year, $month);
        $sumRecurrings = $this->getRecurrings($year, $month);

        return max($balance - $sumRecurrings, 0);
    }

    public function getTotalAmountSpent(string $year, string $month)
    {
        return Transaction::ofSpace(session('space_id'))
            ->whereRaw('YEAR(happened_on) = ? AND MONTH(happened_on) = ?', [$year, $month])
            ->where('type', "spending")
            ->sum('amount');
    }

    public function getDailyBalance(string $year, string $month, ?float $balanceTick = null): array
    {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $dailyBalance = [];
        if (is_null($balanceTick)) {
            $balanceTick = 0;
        }

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $balanceTick -= Transaction::ofSpace(session('space_id'))
                ->where('happened_on', $year . '-' . $month . '-' . $i)
                ->where('type', "spending")
                ->sum('amount');

            $balanceTick += Transaction::ofSpace(session('space_id'))
                ->where('happened_on', $year . '-' . $month . '-' . $i)
                ->where('type', 'earning')
                ->sum('amount');

            $dailyBalance[] = Helper::formatNumber($balanceTick / 100);
        }

        return $dailyBalance;
    }
}
