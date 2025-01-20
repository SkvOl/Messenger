<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class MessageFactory extends Factory
{
    public $chat_id = 1;
    private $user_dop = 2;

    public function definition(): array
    {
        if($this->chat_id == 99) $this->chat_id = 1;
        if($this->user_dop == 100) $this->user_dop = 2;

        $this->chat_id++;
        $this->user_dop++;
        
        if(($this->user_dop - 1) % 2 == 0){
            return [
                'message' => fake()->words(20, true),
                'user_id' => 1,
                'chat_id' => $this->chat_id - 1,
                'viewed_at' => null,
            ];
        }
        else{
            return [
                'message' => fake()->words(20, true),
                'user_id' => ($this->user_dop - 1),
                'chat_id' => $this->chat_id - 1,
                'viewed_at' => null,
            ];
        }
    }
}