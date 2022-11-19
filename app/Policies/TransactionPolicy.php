<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function view(User $user, Transaction $transaction)
    {
        return $user->spaces->contains($transaction->space_id);
    }

    public function edit(User $user, Transaction $transaction)
    {
        return $user->spaces->contains($transaction->space_id);
    }

    public function update(User $user, Transaction $transaction)
    {
        return $user->spaces->contains($transaction->space_id);
    }

    public function delete(User $user, Transaction $transaction)
    {
        return $user->spaces->contains($transaction->space_id);
    }

    public function restore(User $user, Transaction $transaction)
    {
        return $user->spaces->contains($transaction->space_id);
    }
}
