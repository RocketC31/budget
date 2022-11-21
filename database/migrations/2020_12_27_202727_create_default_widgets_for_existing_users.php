<?php

use App\Actions\CreateDefaultWidgetsAction;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach (User::all() as $user) {
            if (!$user->widgets->count()) {
                (new CreateDefaultWidgetsAction())->execute($user->id);
            }
        }
    }

    public function down(): void
    {
        // You can't reverse this
    }
};
