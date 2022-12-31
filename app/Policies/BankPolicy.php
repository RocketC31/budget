<?php

namespace App\Policies;

use App\Models\Bank;
use App\Models\User;

class BankPolicy
{
    public function view(User $user, Bank $bank)
    {
        return $user->spaces->contains($bank->space_id);
    }

    public function edit(User $user, Bank $bank)
    {
        return $user->spaces->contains($bank->space_id);
    }

    public function update(User $user, Bank $bank)
    {
        return $user->spaces->contains($bank->space_id);
    }

    public function delete(User $user, Bank $bank)
    {
        return $user->spaces->contains($bank->space_id);
    }

    public function restore(User $user, Bank $bank)
    {
        return $user->spaces->contains($bank->space_id);
    }
}
