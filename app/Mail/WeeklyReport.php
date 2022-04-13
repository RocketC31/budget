<?php

namespace App\Mail;

use App\Models\Space;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyReport extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected Space $space;
    protected $week;
    protected $totalSpent;
    protected $largestSpendingWithTag;
    protected string $lang;

    public function __construct(Space $space, $week, $totalSpent, $largestSpendingWithTag, string $lang = "en")
    {
        $this->space = $space;
        $this->week = $week;
        $this->totalSpent = $totalSpent;
        $this->largestSpendingWithTag = $largestSpendingWithTag;
        $this->lang = $lang;
        $this->locale($this->lang)
            ->subject(__('email.subject.weekly_report', [], $this->lang));
    }

    public function build()
    {
        return $this
            ->view('emails.weekly_report')
            ->text('emails.weekly_report_plain')
            ->with([
                'space' => $this->space,
                'week' => $this->week,
                'totalSpent' => $this->totalSpent,
                'largestSpendingWithTag' => $this->largestSpendingWithTag
            ]);
    }
}
