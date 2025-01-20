<?php

namespace App\Http\Entities\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OAT;

#[OAT\RequestBody(
    request: 'UserSignupRequest',
    required: true,
    content: new OAT\JsonContent(properties: [
        new OAT\Property(property: 'name', type: 'string', format: 'string', example: 'Oleg', schema:'required|string'),
        new OAT\Property(property: 'email', type: 'string', format: 'email', example: 'dssvsdvsv@mail.ru', schema:'required|string'),
        new OAT\Property(property: 'password', type: 'string', format: 'password', example: 'dssvsdvsv', schema:'required|string'),
    ])
)]
class UserSignupRequest extends FormRequest{

    function rules(): array{

        return [
            'name' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string',
        ];
    }
}