<?php

namespace Tests\Unit\Models;

use App\Helper;
use App\Models\Transaction;
use Tests\TestCase;

class EarningTest extends TestCase
{
    public function testFormattedAmount()
    {
        $earning = Transaction::factory()->make([
            'type' => 'earning',
            'amount' => Helper::rawNumberToInteger(39)
        ]);

        $this->assertEquals('39.00', $earning->formatted_amount);
    }
}
