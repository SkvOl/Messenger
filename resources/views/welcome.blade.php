<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel Reverb WebSocket Test</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.3.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.17.1/echo.iife.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>
<body>
    <h1>Laravel Reverb WebSocket Test</h1>
    <p>Open the console to see WebSocket messages.</p>
    <button id="sendMessage">Send Test Message</button>

    <script>
        $(document).ready(function () {
            $("#sendMessage").click(() => {
                ajax('/api/test/send-message',{
                    message: 'Hello, Reverb 138.124.55.208!'
                });
            });
            
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: '{{ env('REVERB_APP_KEY') }}',
                wsHost: '{{ env('VITE_REVERB_HOST', request()->getHost()) }}',
                wsPort: 9001,
                forceTLS: false,
                enabledTransports: ['ws']
            }); 

            window.Echo.channel('chat-channels')
                .listen('.message-sents', (data) => {
                    console.log(data);
            });
            window.Echo.channel('create-message-20')
                .listen('.create-message', (data) => {
                    console.log(data);
            });
            window.Echo.channel('change-message-20')
                .listen('.change-message', (data) => {
                    console.log(data);
            });
            window.Echo.channel('delete-message-20')
                .listen('.delete-message', (data) => {
                    console.log(data);
            });
            window.Echo.channel('watch-message-20')
                .listen('.watch-message', (data) => {
                    console.log(data);
            });
            window.Echo.channel('update-chats-20')
                .listen('.update-chats', (data) => {
                    console.log(data);
            });
        });



        function ajax(url, data, mmethod = 'post'){
            data['_token'] = '{{csrf_token()}}';

            $.ajax({
                url: url,
                method: mmethod,
                dataType: 'json',
                data: data,
                success: function(response){
                    console.log('Successfully ajax');
                }
            });
        }
    </script>
</body>
</html>