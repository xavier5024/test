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
use Illuminate\Support\Facades\DB;

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
      $insert_arr = array();
      $insert_arr["id"] = Auth::id();
      $insert_arr["content"] = request()->message;
      if (request()->hasFile('attachments') && request()->attachments->isValid()) {
          $logical_name = request()->file('attachments')->getClientOriginalName();
          $ext = substr($logical_name, strrpos($logical_name, "."));
          $physical_name = round(microtime(true)).$ext;
          $filepath = public_path("data");
          $path = "/".date("Y")."/".date("md");
          if (!is_dir(public_path("data")."/".date("Y"))) {
              mkdir(public_path("data")."/".date("Y"));
          }
          if (!is_dir(public_path("data")."/".$path)) {
              mkdir(public_path("data")."/". $path);
          }
          
          $file_result = request()->file('attachments')->move($filepath.$path,$physical_name);
          if ($file_result) {
              if (preg_match("/\.(gif|jpg|jpeg|png)$/i", $ext)) {
                  //이미지확장자
                  $insert_arr["content"] .= "<a href='/data".$path."/".$physical_name."'><img class='upload_img' src='/data/".$path."/".$physical_name."'/></a>";
                  $data["message"] .= "<a href='/data".$path."/".$physical_name."'><img class='upload_img' src='/data/".$path."/".$physical_name."'/></a>";
              } else {
                  $insert_arr["content"] .= "<a href='/data".$path."/".$physical_name."'><button type='button' class='btn btn-primary'>".$logical_name."</button></a>";
                  $data["message"] .= "<a href='/data".$path."/".$physical_name."'><button type='button' class='btn btn-primary'>".$logical_name."</button></a>";
              }
              $insert_arr["file"] = "/data".$path."/".$physical_name;
          }
      }
      DB::table('common_chats')->insert($insert_arr);
      return [
		"data"   => $data,
		"user"	=> $user
      ];
    }
}