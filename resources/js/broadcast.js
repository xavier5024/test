
import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'local',
    wsHost: '192.168.1.76',
    wsPort: 6001,
    forceTLS: false,
    disableStates:true
});