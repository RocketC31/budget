<?php

namespace Tests\Unit\Actions\Space;

use App\Actions\CreateSpaceAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreationTest extends TestCase
{
    use RefreshDatabase;

    public function testSuccessfulCreation(): void
    {
        $user = User::factory()->create();
        $space = (new CreateSpaceAction())->execute('Testing Space', 1, $user->id);

        $this->assertNotNull($space->id);
        $this->assertCount(1, $space->users);
    }
}
