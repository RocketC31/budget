<?php

namespace App\Events;

use App\Models\Activity;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Facades\Auth;

class TransactionCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct($transaction)
    {
        $userId = null;

        if (Auth::check()) {
            $userId = Auth::user()->id;
        }

        Activity::create([
            'space_id' => $transaction->space_id,
            'user_id' => $userId,
            'entity_id' => $transaction->id,
            'entity_type' => $transaction->type,
            'action' => 'transaction.created'
        ]);
    }
}
