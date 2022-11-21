<?php

namespace Tests\Unit\Models;

use App\Helper;
use App\Models\Transaction;
use Tests\TestCase;
use App\Models\Space;

class SpaceTest extends TestCase
{
    public function testMonthlyBalance()
    {
        $space = Space::factory()->create();

        Transaction::factory()->create([
            'type' => 'earning',
            'space_id' => $space->id,
            'amount' => Helper::rawNumberToInteger(39),
            'happened_on' => now()
        ]);

        $this->assertEquals('3900', $space->monthlyBalance(now()->year, now()->month));

        Transaction::factory()->create([
            'type' => 'spending',
            'space_id' => $space->id,
            'amount' => Helper::rawNumberToInteger(12),
            'happened_on' => now()
        ]);

        $this->assertEquals('2700', $space->monthlyBalance(now()->year, now()->month));

        Transaction::factory()->create([
            'type' => 'spending',
            'space_id' => $space->id,
            'amount' => Helper::rawNumberToInteger(50),
            'happened_on' => now()
        ]);

        // Monthly balance can be negative
        $this->assertEquals('-2300', $space->monthlyBalance(now()->year, now()->month));

        Transaction::factory()->create([
            'type' => 'earning',
            'space_id' => $space->id,
            'amount' => Helper::rawNumberToInteger(10),
            'happened_on' => date_create_from_format('Y-m-d', '2018-01-01')
        ]);

        // First is same as before, because earning happened on a different month
        $this->assertEquals('-2300', $space->monthlyBalance(now()->year, now()->month));
        $this->assertEquals('1000', $space->monthlyBalance(2018, 01));

        Transaction::factory()->create([
            'type' => 'earning',
            'space_id' => 2,
            'amount' => Helper::rawNumberToInteger(10),
            'happened_on' => date_create_from_format('Y-m-d', '2020-01-01')
        ]);

        // 0 because different space-id
        $this->assertEquals('0', $space->monthlyBalance(2020, 01));
    }
}
