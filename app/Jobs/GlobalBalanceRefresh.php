<?php

namespace App\Jobs;

use App\Models\Widget;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GlobalBalanceRefresh implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    public function handle(): void
    {
        //TODO : filter where user not deleted ?
        $widgets = Widget::where("type", "balance_global")->get();
        foreach ($widgets as $widget) {
            $widget = $widget->resolve();
            $widget->refreshRedisBalance();
        }
    }
}
