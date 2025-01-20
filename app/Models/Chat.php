<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Message;
use App\Models\User;

class Chat extends Model{
    /** @use HasFactory<\Database\Factories\ChatFactory> */
    use HasFactory;

    public $table = 'chats';
    public $timestamps = true;
    const UPDATED_AT = null;

    public function User1()
    {
        return $this->belongsTo(User::class, 'user_id1', 'id');
    }

    public function User2()
    {
        return $this->belongsTo(User::class, 'user_id2', 'id');
    }

    public function Message()
    {
        return $this->hasMany(Message::class, 'chat_id', 'id')->orderBy('id', 'desc');
    }

    public function latestMessage() {
        return $this->hasOne(Message::class, 'chat_id', 'id')->orderBy('id', 'desc')->take(1);
    }
}