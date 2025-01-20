<?php
namespace App\Http\Entities\User\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Source\Controller;
use OpenApi\Attributes as OAT;
use App\Http\Source\Response;
use Illuminate\Http\Request;
use App\Http\Entities\User\Requests\UserRequest;
use App\Http\Entities\User\Requests\UserSigninRequest;
use App\Http\Entities\User\Requests\UserSignupRequest;
use App\Models\User;

class UserController extends Controller{
    use Response;


    #[OAT\Post(
        path: '/signup',
        summary: 'Регистрация пользователя',
        description: 'Регистрация пользователя',
        tags: ['user'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Успешно',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(property: 'status', type: 'string', format: 'string', example: 'Successfully'),
                    new OAT\Property(property: 'paginator', ref: '#/components/schemas/Paginator'),
                    new OAT\Property(property: 'data', type: 'array', items: new OAT\Items(
                        properties: [
                            new OAT\Property(property: 'token', type: 'string', format: 'jwt', example: '2|y8TluLPvLbGywAKL2ZSTGMTBi7yXWiH1jpwWDFSVfb2a1a49'),
                            new OAT\Property(property: 'user', type: 'array', items: new OAT\Items(
                                properties: [
                                    new OAT\Property(property: 'id', type: 'int', format: 'int', example: '1'),
                                    new OAT\Property(property: 'name', type: 'string', format: 'name', example: 'Oleg'),
                                    new OAT\Property(property: 'email', type: 'string', format: 'email', example: '11@mail.ru'),
                                    new OAT\Property(property: 'created_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                                ]
                            )),
                        ]
                    ))
                ])
            ),
        ],
        parameters: [new OAT\RequestBody(ref: "#/components/requestBodies/UserSignupRequest")]
    )]
    public function signup(UserSignupRequest $request)
    {
        $user = new User;

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = md5($request->input('password').env('SALT'));

        $user->save();

        return self::response([
            'token' => $user->createToken($user->name)->plainTextToken,
            'user' => $user
        ]);;
    }


    #[OAT\Post(
        path: '/signin',
        summary: 'Авторизация пользователя',
        description: 'Авторизация пользователя',
        tags: ['user'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Успешно',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(property: 'status', type: 'string', format: 'string', example: 'Successfully'),
                    new OAT\Property(property: 'paginator', ref: '#/components/schemas/Paginator'),
                    new OAT\Property(property: 'data', type: 'array', items: new OAT\Items(
                        properties: [
                            new OAT\Property(property: 'token', type: 'string', format: 'jwt', example: '2|y8TluLPvLbGywAKL2ZSTGMTBi7yXWiH1jpwWDFSVfb2a1a49'),
                            new OAT\Property(property: 'user', type: 'array', items: new OAT\Items(
                                properties: [
                                    new OAT\Property(property: 'id', type: 'int', format: 'int', example: '1'),
                                    new OAT\Property(property: 'name', type: 'string', format: 'name', example: 'Oleg'),
                                    new OAT\Property(property: 'email', type: 'string', format: 'email', example: '11@mail.ru'),
                                    new OAT\Property(property: 'created_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                                ]
                            )),
                        ]
                    ))
                ])
            ),
        ],
        parameters: [new OAT\RequestBody(ref: "#/components/requestBodies/UserSigninRequest")]
    )]
    public function signin(UserSigninRequest $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => md5(($request->password).env('SALT'))])){ 
            $user = Auth::user(); 

            return self::response([
                'token' => $user->createToken($user->name)->plainTextToken,
                'user'=>$user
            ]);
        } 
        else{ 
            return self::response('Invalid username or password', 401);
        } 
    }

    #[OAT\Get(
        path: '/user',
        summary: 'Получение списка пользователей по их имени',
        description: 'Получение списка пользователей по их имени',
        tags: ['user'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Успешно',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(property: 'status', type: 'string', format: 'string', example: 'Successfully'),
                    new OAT\Property(property: 'paginator', ref: '#/components/schemas/Paginator'),
                    new OAT\Property(property: 'data', type: 'array', items: new OAT\Items(
                        properties: [
                            new OAT\Property(property: 'id', type: 'int', format: 'int', example: '1'),
                            new OAT\Property(property: 'name', type: 'string', format: 'name', example: 'Oleg'),
                            new OAT\Property(property: 'email', type: 'string', format: 'email', example: '11@mail.ru'),
                            new OAT\Property(property: 'created_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                        ]
                    ))
                ])
            ),
        ],
        parameters: [new OAT\RequestBody(ref: "#/components/requestBodies/UserRequest")]
    )]
    public function user(UserRequest $request)
    {
        $name = urldecode($request->name);
        $user_id = $request->user()->id;
        
        return self::response(User::where('name', 'LIKE', '%'.$name.'%')->where('id', '!=', $user_id)->
        paginate(perPage: env('PER_PAGE'), page: $request->page));
    }


    #[OAT\Get(
        path: '/current_user',
        summary: 'Получение текущего пользователя',
        description: 'Получение текущего пользователя',
        tags: ['user'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Успешно',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(property: 'status', type: 'string', format: 'string', example: 'Successfully'),
                    new OAT\Property(property: 'paginator', ref: '#/components/schemas/Paginator'),
                    new OAT\Property(property: 'data', type: 'array', items: new OAT\Items(
                        properties: [
                            new OAT\Property(property: 'id', type: 'int', format: 'int', example: '1'),
                            new OAT\Property(property: 'name', type: 'string', format: 'name', example: 'Oleg'),
                            new OAT\Property(property: 'email', type: 'string', format: 'email', example: '11@mail.ru'),
                            new OAT\Property(property: 'created_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                        ]
                    ))
                ])
            ),
        ],
    )]
    public function current_user(Request $request)
    {
        return self::response($request->user());
    }

    #[OAT\Post(
        path: '/logout',
        summary: 'Выход из аккаунта пользователя',
        description: 'Выход из аккаунта пользователя',
        tags: ['user'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Успешно',
            ),
        ],
    )]
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
    
        return self::response(statusCode: 200);
    }
}