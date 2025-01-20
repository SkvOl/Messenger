<?php
namespace App\Http\Entities\Chat\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource{
    function toArray($request){
		
        if (isset($this->latest_message) AND $this->latest_message != 'null'){
            $latest_message = json_decode($this->latest_message, true);

            return [
                'id'=>$this->chat_id,
                'user_id1'=>$this->user_id1,
                'user_id2'=>$this->user_id2,
                'created_at'=>$this->created_at_chats,
                'id'=>$this->chat_id,
                'user1'=>[
                    'id'=>$this->user_id1,
                    'name'=>$this->name_user1,
                    'email'=>$this->email_user1,
                    'created_at'=>$this->created_at_user1,
                ],
                'user2'=>[
                    'id'=>$this->user_id2,
                    'name'=>$this->name_user2,
                    'email'=>$this->email_user2,
                    'created_at'=>$this->created_at_user2,
                ],
                'latest_message'=>$latest_message
            ];	
        }
        else return ['id'=>$this->chat_id];
    }
}