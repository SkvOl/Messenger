<?php

namespace App\Http\Entities\Message\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OAT;

#[OAT\RequestBody(
    request: 'MessageChangeRequest',
    required: true,
    content: new OAT\JsonContent(properties: [
        new OAT\Property(property: 'message', type: 'string', format: 'string', example: 'dssvsdvsv', schema:'required|string'),
    ])
)]
class MessageChangeRequest extends FormRequest{

    function rules(): array{

        return [
            'message' => 'required|string',
        ];
    }
}