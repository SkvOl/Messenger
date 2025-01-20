<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Chat;

class Message extends Model
{
    /** @use HasFactory<\Database\Factories\MessageFactory> */
    use HasFactory;

    public $table = 'messages';
    public $timestamps = true;

    public function Chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id', 'id');
    }
}