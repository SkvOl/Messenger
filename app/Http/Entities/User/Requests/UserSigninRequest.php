<?php

namespace App\Http\Entities\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OAT;

#[OAT\RequestBody(
    request: 'UserSigninRequest',
    required: true,
    content: new OAT\JsonContent(properties: [
        new OAT\Property(property: 'email', type: 'string', format: 'email', example: 'dssvsdvsv@mail.ru', schema:'required|string'),
        new OAT\Property(property: 'password', type: 'string', format: 'password', example: 'dssvsdvsv', schema:'required|string'),
    ])
)]
class UserSigninRequest extends FormRequest{

    function rules(): array{

        return [
            'email' => 'required|string',
            'password' => 'required|string',
        ];
    }
}