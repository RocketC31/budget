<?php

namespace App\Jobs;

use App\Actions\CreateUserAction;
use App\Repositories\SpaceRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class ResetDatabaseDemoMode implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(SpaceRepository $spaceRepository): void
    {
        if (config("app.demo_mode")) {
            Artisan::call("migrate:fresh");
            $user = (new CreateUserAction())->execute(
                "demo",
                "demo@demo.com",
                "demo"
            );
            $user->update(['verification_token' => null]);
            $space = $spaceRepository->create(2, $user->name . '\'s Space');
            $user->spaces()->attach($space->id, ['role' => 'admin']);
        }
    }
}
