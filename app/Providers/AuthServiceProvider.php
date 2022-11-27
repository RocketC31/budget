<?php

namespace App\Providers;

use App\Models\Bank;
use App\Models\Import;
use App\Models\Transaction;
use App\Policies\BankPolicy;
use App\Policies\ImportPolicy;
use App\Policies\TagPolicy;
use App\Models\Tag;
use App\Policies\TransactionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Policies\RecurringPolicy;
use App\Models\Recurring;
use App\Models\Space;
use App\Models\SpaceInvite;
use App\Policies\SpaceInvitePolicy;
use App\Policies\SpacePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        Space::class => SpacePolicy::class,
        SpaceInvite::class => SpaceInvitePolicy::class,
        Transaction::class => TransactionPolicy::class,
        Recurring::class => RecurringPolicy::class,
        Tag::class => TagPolicy::class,
        Import::class => ImportPolicy::class,
        Bank::class => BankPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
