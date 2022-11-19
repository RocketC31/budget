<?php

namespace Tests\Feature;

use App\Models\Space;
use App\Models\Transaction;
use App\Models\User;
use Tests\TestCase;

class SpendingTest extends TestCase
{
    public function testUnauthorizedUserCantDeleteSpending()
    {
        $user = User::factory()->create();

        $space = Space::factory()->create();

        $spending = Transaction::factory()->create([
            'type' => 'spending',
            'space_id' => $space->id
        ]);

        $this->actingAs($user);

        $response = $this->delete('/transactions/' . $spending->id);

        $response->assertStatus(403);
    }
}
