<?php

namespace App\Listeners;

use App\Events\ResetList;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ResetListNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ResetList  $event
     * @return void
     */
    public function handle(ResetList $event)
    {
        //
    }
}
