<?php

namespace Tests\Feature;

use App\Models\Space;
use App\Models\Transaction;
use App\Models\User;
use Tests\TestCase;

class EarningTest extends TestCase
{
    public function testUnauthorizedUserCantDeleteEarning()
    {
        $user = User::factory()->create();

        $space = Space::factory()->create();

        $earning = Transaction::factory()->create([
            'space_id' => $space->id,
            'type' => 'earning'
        ]);

        $this->actingAs($user);

        $response = $this->delete('/transactions/' . $earning->id);

        $response->assertStatus(403);
    }
}
