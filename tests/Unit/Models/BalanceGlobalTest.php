<?php

namespace Tests\Unit\Models;

use App\Helper;
use App\Models\Space;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Widget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalanceGlobalTest extends TestCase
{
    use RefreshDatabase;

    private Space $space;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->space = Space::factory()->create();
        $this->user = User::factory()->create();
        $this->withSession(['space_id' => $this->space->id]);
        //Disabled redis for not impact test
        config(["app.redis_available" => false]);
    }

    private function addDefaultEarningAndSpending(): float
    {
        $currentDate = new \DateTime();
        //Added earning
        Transaction::factory()->create([
            'type' => 'earning',
            'space_id' => $this->space->id,
            'happened_on' => $currentDate->format('Y-m-d'),
            'amount' => Helper::rawNumberToInteger(150)
        ]);

        //Added spending
        Transaction::factory()->create([
            'type' => 'spending',
            'space_id' => $this->space->id,
            'happened_on' => $currentDate->format('Y-m-d'),
            'amount' => Helper::rawNumberToInteger(71.20)
        ]);

        return (150 - 71.20);
    }

    public function testBalanceOnSameMonth(): void
    {
        $defaultDifference = $this->addDefaultEarningAndSpending();

        $widget = Widget::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'balance_global'
        ]);
        $widget = $widget->resolve();

        $this->assertEquals($defaultDifference, $widget->balance);
    }

    public function testBalanceOnDifferentMonth(): void
    {
        $defaultDifference = $this->addDefaultEarningAndSpending();

        $oldDate = new \DateTime();
        $oldDate->sub(new \DateInterval("P3M"));
        //Add an other spending in older month
        Transaction::factory()->create([
            'type' => 'spending',
            'space_id' => $this->space->id,
            'happened_on' => $oldDate->format('Y-m-d'),
            'amount' => Helper::rawNumberToInteger(14.27)
        ]);

        $widget = Widget::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'balance_global'
        ]);
        $widget = $widget->resolve();

        $this->assertEquals(($defaultDifference - 14.27), $widget->balance);
    }

    public function testBalanceWithRemovedData()
    {
        $defaultDifference = $this->addDefaultEarningAndSpending();

        $oldDate = new \DateTime();
        $oldDate->sub(new \DateInterval("P3M"));

        //Add an other spending in older month
        Transaction::factory()->create([
            'type' => 'spending',
            'space_id' => $this->space->id,
            'happened_on' => $oldDate->format('Y-m-d'),
            'deleted_at' => $oldDate->format('Y-m-d'),
            'amount' => Helper::rawNumberToInteger(14.27)
        ]);

        $widget = Widget::factory()->create([
            'user_id' => $this->user->id,
            'type' => 'balance_global'
        ]);

        $widget = $widget->resolve();

        $this->assertEquals($defaultDifference, $widget->balance);
    }
}
