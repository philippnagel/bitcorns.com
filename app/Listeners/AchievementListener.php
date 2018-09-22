<?php

namespace App\Listeners;

use Droplister\XcpCore\App\Block;
use Gstt\Achievements\Event\Unlocked;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AchievementListener
{
    /**
     * Handle the event.
     *
     * @param  \Gstt\Achievements\Event\Unlocked  $event
     * @return void
     */
    public function handle(Unlocked $event)
    {
        // Get Block
        $block = Block::latest('block_index')->first();

        // Timestamp
        $event->progress->update([
            'unlocked_at' => $block->confirmed_at,
        ]);
    }
}
