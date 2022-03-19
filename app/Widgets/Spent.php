<?php

namespace App\Widgets;

use App\Helper;
use App\Models\Spending;
use App\Models\Widget;

class Spent extends Widget
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->appends = array_merge($this->appends, ['period', 'spent']);
    }

    public function getPeriodAttribute()
    {
        return $this->properties->period;
    }

    public function getSpentAttribute()
    {
        $spent = null;
        if ($this->properties->period === 'today') {
            $spent = Spending::ofSpace(session('space_id'))
                ->whereRaw('DATE(happened_on) = ?', [date('Y-m-d')])
                ->sum('amount');
        }

        if ($this->properties->period === 'this_week') {
            $monday = date('Y-m-d', strtotime('monday this week'));
            $sunday = date('Y-m-d', strtotime('sunday this week'));

            $spent = Spending::ofSpace(session('space_id'))
                ->whereRaw('DATE(happened_on) >= ? AND DATE(happened_ON) <= ?', [$monday, $sunday])
                ->sum('amount');
        }

        if ($this->properties->period === 'this_month') {
            $spent = Spending::ofSpace(session('space_id'))
                ->whereRaw('YEAR(happened_on) = ? AND MONTH(happened_on) = ?', [date('Y'), date('n')])
                ->sum('amount');
        }

        return Helper::formatNumber($spent / 100);
    }
}
