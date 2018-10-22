<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ImageUploaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $path;
    public $file_name;
    public $original_file_name;
    public $staff_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($path, $file_name, $original_file_name, $staff_id)
    {
        $this->path = $path;
        $this->file_name = $file_name;
        $this->original_file_name = $original_file_name;
        $this->staff_id = $staff_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
