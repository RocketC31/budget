<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateUserAction
{
    public function execute(string $name, string $email, string $password): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'language' => App::getLocale(),
            'verification_token' => Str::random(100)
        ]);

        (new CreateDefaultWidgetsAction())->execute($user->id);

        return $user;
    }
}
