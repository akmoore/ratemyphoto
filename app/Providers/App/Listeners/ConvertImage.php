<?php

namespace App\Providers\App\Listeners;

use App\Providers\App\Events\Imageuploaded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConvertImage
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
     * @param  Imageuploaded  $event
     * @return void
     */
    public function handle(Imageuploaded $event)
    {
        //
    }
}
