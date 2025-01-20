<?php

namespace App\Http\Entities\Message\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OAT;

#[OAT\RequestBody(
    request: 'MessageCreateRequest',
    required: true,
    content: new OAT\JsonContent(properties: [
        new OAT\Property(property: 'chat_id', type: 'int', format: 'int', example: '4', schema:'required|int'),
        new OAT\Property(property: 'message', type: 'string', format: 'string', example: 'dssvsdvsv', schema:'required|string'),
    ])
)]
class MessageCreateRequest extends FormRequest{

    function rules(): array{

        return [
            'chat_id'=>'required|int',
            'message' => 'required|string',
        ];
    }
}