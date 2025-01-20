<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ChatFactory extends Factory
{

    private $user_dop = 2;

    public function definition(): array
    {
        $this->user_dop++;

        if(($this->user_dop - 1) % 2 == 0){
            return [
                'user_id1' => 1,
                'user_id2' => ($this->user_dop - 1)
            ];
        }
        else{
            return [
                'user_id1' => ($this->user_dop - 1),
                'user_id2' => 1
            ];
        }
        
    }
}