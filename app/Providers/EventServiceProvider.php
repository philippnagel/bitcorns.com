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
        'Droplister\XcpCore\App\Events\CreditWasCreated' => [
            'App\Listeners\FarmListener',
        ],
        'Droplister\XcpCore\App\Events\SendWasCreated' => [
            'App\Listeners\MuseumListener',
        ],
        'Droplister\XcpCore\App\Events\BalanceWasUpdated' => [
            'App\Listeners\NoCropsListener',
        ],
        'Droplister\XcpCore\App\Events\DividendWasCreated' => [
            'App\Listeners\HarvestListener',
        ],
        'Gstt\Achievements\Event\Unlocked' => [
            'App\Listeners\AchievementListener',
        ],
        'App\Events\TokenWasCreated' => [
            'App\Listeners\SubmissionListener',
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
