<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // Game Play
        'Droplister\XcpCore\App\Events\CreditWasCreated' => [
            'App\Listeners\CreateFarmsFromCredits',
        ],
        'Droplister\XcpCore\App\Events\BalanceWasUpdated' => [
            'App\Listeners\AccessDependentOnCropsBalance',
        ],
        'Droplister\XcpCore\App\Events\DividendWasCreated' => [
            'App\Listeners\UpdateHarvestsOnDividend',
        ],
        // Achievements
        'Gstt\Achievements\Event\Unlocked' => [
            'App\Listeners\RelativeAchievementTimestamps',
        ],
        'Droplister\XcpCore\App\Events\BalanceWasCreated' => [
            'App\Listeners\TokenBalanceAchievements',
        ],
        'Droplister\XcpCore\App\Events\OrderMatchWasCreated' => [
            'App\Listeners\TokenTradingAchievements',
        ],
        // Bitcorn Cards
        'App\Events\TokenWasCreated' => [
            'App\Listeners\AnnounceNewCardSubmissions',
        ],
        'Droplister\XcpCore\App\Events\SendWasCreated' => [
            // Bitcorn Cards
            'App\Listeners\MonitorMuseumDeposits',
            'App\Listeners\MonitorSubmissionFees',
            // Achievements
            'App\Listeners\FarmSendAchievements',
            'App\Listeners\TokenSendAchievements',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
