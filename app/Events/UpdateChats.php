<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateChats implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;

    /**
     * Create a new event instance.
     */
    public function __construct(array $chat)
    {
        $this->chat = $chat;
        file_put_contents('message_sent_test.txt', date('Y-m-d H:i:s').'  '.var_export($this->chat, true)."\n", FILE_APPEND);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('update-chats-'.$this->chat['user_id1']),
            new Channel('update-chats-'.$this->chat['user_id2']),
        ];
    }

    public function broadcastAs(): string
    {
        return 'update-chats';
    }
}
