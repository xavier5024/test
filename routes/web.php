<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\privateWhisper;
use App\Notifications\notiClear;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	$user = Auth::user();
	$common_chat = DB::table('common_chats AS cc')->select("cc.id", "cc.content", "cc.regDate", "users.name", "users.profile_src")->join("users","cc.id", "users.id")->get();
	return view("broadcasting", array("user"=>$user, "common_chat"=>$common_chat));
})->middleware('auth');

//Route::get('/test', [HomeController::class,'test']);

Route::post('/broadcast', function(Request $request){
	$user = Auth::user();
	DB::table('common_chats')->insert(array("id"=>Auth::id(),"content"=>$request->message));
	broadcast(new \App\Events\Hello("common"));
	return "";
});

Route::match(array('GET', 'POST'), '/broadcasting', function(){
	$user = Auth::user();
	$common_chat = DB::table('common_chats AS cc')->select("cc.id", "cc.content", "cc.regDate", "users.name", "users.profile_src")->join("users","cc.id", "users.id")->get();
	return view("broadcasting", array("user"=>$user, "common_chat"=>$common_chat));
})->middleware('auth');


Route::post('/privateBroadcast', function(Request $request){
	$channelname = $request->get("channelname");
	$privateTo = $request->get("privateTo");
	$message = $request->get("message");
	$user = User::find($privateTo);
	$me = Auth::user();
	$user->notify(new privateWhisper($me->id, $privateTo, $message));
	broadcast(new \App\Events\Hello($channelname));
	return "";
});

Route::post('/privateRead', function(Request $request){
	$privateTo = $request->get("privateTo");
	$user = Auth::user();
	$user->unreadNotifications()->where('type', 'App\Notifications\privateWhisper')->where("data->send_id", $privateTo)->get()->map(function($n) {
		$n->markAsRead();
	});	
	$user->notify(new notiClear($privateTo));
	return "";
});

Route::get('/privateChat/{to}', function($to){
	$user = Auth::user();
	$privateTo = User::find($to);
	$channelname = ($user->id < $privateTo->id) ? $user->id."to".$privateTo->id : $privateTo->id."to".$user->id;
	$chat = DB::table('notifications')->where("notifiable_id", $user->id)->where("data->send_id", $privateTo->id);
	$chatting = DB::table('notifications')->where("notifiable_id", $privateTo->id)->where("data->send_id", $user->id)->union($chat)->orderBy("created_at")->get()->toArray();
	$user->unreadNotifications()->where('type', 'App\Notifications\privateWhisper')->where("data->send_id", $privateTo->id)->get()->map(function($n) {
		$n->markAsRead();
	});	
	$user->notify(new notiClear($privateTo->id));

	return view("privateChat", array("user"=>$user, "privateTo"=>$privateTo, "chatting"=>$chatting, "channelname"=>$channelname));
})->middleware('auth');

Auth::routes();

Route::get('/home', function(Request $request){
	return redirect('/'); 
});
