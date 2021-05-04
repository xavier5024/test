<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class privateWhisper extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    private $send_id;
    private $message;
    private $privateTo;
    
    public function __construct($send_id, $privateTo, $message)
    {
        $this->send_id = $send_id;
        $this->message = $message;
        $this->privateTo = $privateTo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'send_id' => (int)$this->send_id,
            'message' => $this->message
        ];
    }

    public function toBroadcast($notifiable)
    {
        $user = User::find($this->privateTo);
        $noti_cnt = $user->unreadNotifications()->where('data->send_id', $this->send_id)->count();
        //$noti_cnt = DB::table("notifications")->where("notifiable_id", $user->id)->where('data->send_id', $this->send_id)->count();
        return new BroadcastMessage([
            'send_id' => (int)$this->send_id,
            'message' => $this->message,
            'noti_cnt' => $noti_cnt,
            'result' => "add",
            'user'  =>$user
        ]);
    }
}
