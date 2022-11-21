<?php

namespace Tests\Unit\Models;

use App\Helper;
use App\Models\Transaction;
use Tests\TestCase;

class SpendingTest extends TestCase
{
    public function testFormattedAmount()
    {
        $spending = Transaction::factory()->make([
            'type' => 'spending',
            'amount' => Helper::rawNumberToInteger(92.35)
        ]);

        $this->assertEquals('92.35', $spending->formatted_amount);
    }
}
