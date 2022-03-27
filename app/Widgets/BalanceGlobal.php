<?php

namespace App\Widgets;

use App\Helper;
use App\Models\Widget;
use App\Repositories\DashboardRepository;
use Illuminate\Support\Facades\Redis;

class BalanceGlobal extends Widget
{
    private DashboardRepository $dashboardRepository;

    public function __construct()
    {
        parent::__construct();
        $this->appends = array_merge($this->appends, ['balance', 'can_refresh']);
        $this->dashboardRepository = new DashboardRepository();
    }

    /**
     * @return string
     */
    public function getBalanceAttribute(): string
    {
        $spaceId = session("space_id");
        if (config("app.redis_available", true)) {
            try {
                $balance = Redis::get("global_balance:space:" . $spaceId);
                if (!is_null($balance)) {
                    return $balance;
                }
            } catch (\Exception $e) {
            }

            $this->refreshRedisBalance();
        }

        return $this->countGlobalBalance($spaceId, new \DateTime());
    }

    public function getCanRefreshAttribute(): bool
    {
        return config("app.redis_available", true);
    }

    /**
     * Calcule and resave global balance in redis
     * @return void
     */
    public function refreshRedisBalance()
    {
        $this->load("user");
        $spaceId = $this->user->spaces[0]->id;
        //Refresh on total
        Redis::set("global_balance:space:" . $spaceId, $this->countGlobalBalance($spaceId, new \DateTime()));

        //Refresh on start month for daily report
        Redis::set("global_balance:start_month:space:" . $spaceId, $this->countGlobalBalance($spaceId));
    }

    /**
     * Count the global balance
     * @param int $spaceId
     * @param \DateTime|null $endDate
     * @return string
     */
    public function countGlobalBalance(int $spaceId, ?\DateTime $endDate = null): string
    {
        $balance = $this->dashboardRepository->getBalanceGlobal($spaceId, $endDate);
        return Helper::formatNumber($balance / 100);
    }

    public function removeRedisKeys()
    {
        if (config("app.redis_available")) {
            $spaceId = session("space_id");
            //Refresh on total
            Redis::del("global_balance:space:" . $spaceId);
            //Refresh on start month for daily report
            Redis::del("global_balance:start_month:space:" . $spaceId);
        }
    }
}
