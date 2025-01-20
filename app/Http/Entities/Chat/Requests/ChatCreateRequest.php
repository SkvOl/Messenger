<?php

namespace App\Http\Entities\Chat\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OAT;

#[OAT\RequestBody(
    request: 'ChatCreateRequest',
    required: true,
    content: new OAT\JsonContent(properties: [
        new OAT\Property(property: 'user_id2', type: 'int', format: 'int', example: '4', schema:'required|int'),
        new OAT\Property(property: 'message', type: 'string', format: 'string', example: 'dssvsdvsv', schema:'required|string'),
    ])
)]
class ChatCreateRequest extends FormRequest{

    function rules(): array{

        return [
            'user_id2'=>'required|int',
            'message' => 'required|string',
        ];
    }
}