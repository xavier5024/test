<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('monitoring.{id}', function ($user, $id) {
    $user_noti = $user->unreadNotifications()->select("data")->where('type', 'App\Notifications\privateWhisper')->get()->groupBy('data.send_id')->toArray();
    $user_notis = array();
    if($user_noti){
        foreach($user_noti as $key => $val){
            $arr = array();
            $arr['send_id'] = $key;
            $arr['send_cnt'] = count($val);
            $user_notis[]= $arr;
        }
    }
    $user->noti = $user_notis;
    return $user;
});

Broadcast::channel('App.User.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});