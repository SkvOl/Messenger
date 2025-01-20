<?php

namespace App\Http\Entities\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OAT;

#[OAT\RequestBody(
    request: 'UserRequest',
    required: true,
    content: new OAT\JsonContent(properties: [
        new OAT\Property(property: 'name', type: 'string', format: 'string', example: 'Oleg', schema:'required|string'),
        new OAT\Property(property: 'page', type: 'int', format: 'int', example: '2', schema:'required|integer'),
    ])
)]
class UserRequest extends FormRequest{

    function rules(): array{

        return [
            'name' => 'required|string',
            'page' => 'integer',
        ];
    }
}