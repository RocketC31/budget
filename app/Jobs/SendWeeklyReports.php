<?php

namespace App\Jobs;

use App\Helper;
use App\Mail\WeeklyReport;
use App\Models\Space;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendWeeklyReports implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        $spaces = Space::all();
        $week = date('W');
        $lastWeekDate = date('Y-m-d', strtotime('-7 days'));
        $currentDate = date('Y-m-d');

        foreach ($spaces as $space) {
            $totalSpent = DB::select('
                SELECT SUM(amount) AS foo
                FROM spendings
                WHERE space_id = ? AND happened_on >= ? AND happened_on <= ?
            ', [$space->id, $lastWeekDate, $currentDate])[0]->foo;

            $largestSpendingWithTag = DB::select('
                SELECT SUM(spendings.amount) AS amount, tags.name AS tag_name
                FROM spendings
                INNER JOIN tags ON spendings.tag_id = tags.id
                WHERE spendings.space_id = ?
                    AND spendings.happened_on >= ?
                    AND spendings.happened_on <= ?
                    AND spendings.tag_id IS NOT NULL
                GROUP BY tag_name', [$space->id, $lastWeekDate, $currentDate]);

            foreach ($space->users as $user) {
                // If plans are enabled, weekly reports can only be sent to users with a premium plan
                if (Helper::arePlansEnabled() && $user->plan === 'standard') {
                    continue;
                }

                // Only send if user wants to receive report
                if ($user->weekly_report) {
                    try {
                        Mail::to($user->email)->queue(new WeeklyReport(
                            $space,
                            $week,
                            $totalSpent,
                            $largestSpendingWithTag,
                            $user->language
                        ));
                    } catch (\Exception $e) {
                    }
                }
            }
        }
    }
}
