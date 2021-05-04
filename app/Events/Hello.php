<?php

namespace App\Events;

use Illuminate\Support\Facades\Auth;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Hello implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
	private $user;
    private $user_id;

    public function __construct($user_id)
    {
        //
		//$this->user = $user;
        $this->user_id = $user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // redis채널이름 
        return new PresenceChannel('monitoring.'.$this->user_id);
    }

    public function broadcastWith()
    {
      $user = Auth::user();
	  $data = request()->all();
	  $data["message"] = $data["message"];

      return [
		"data"   => $data,
		"user"	=> $user
      ];
    }
}