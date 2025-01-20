<?php
namespace App\Http\Entities\Chat\Controllers;

use App\Http\Entities\Chat\Resources\ChatResource;
use Illuminate\Support\Facades\DB;
use App\Http\Source\Controller;
use OpenApi\Attributes as OAT;
use App\Http\Source\Response;
use Illuminate\Http\Request;
use App\Http\Entities\Chat\Requests\ChatCreateRequest;
use App\Events\UpdateChats;
use App\Models\Message;
use App\Models\Chat;


class ChatController extends Controller{
    use Response;

    #[OAT\Get(
        path: '/chat',
        summary: 'Получение списка чатов пользователя',
        description: 'Получение списка чатов пользователя',
        tags: ['chat'],
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
                            new OAT\Property(property: 'user_id1', type: 'int', format: 'int', example: '23'),
                            new OAT\Property(property: 'user_id2', type: 'int', format: 'int', example: '11'),
                            new OAT\Property(property: 'created_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                            new OAT\Property(property: 'user1', type: 'array', items: new OAT\Items(
                                properties: [
                                    new OAT\Property(property: 'id', type: 'int', format: 'int', example: '1'),
                                    new OAT\Property(property: 'name', type: 'string', format: 'name', example: 'Oleg'),
                                    new OAT\Property(property: 'email', type: 'string', format: 'email', example: '11@mail.ru'),
                                    new OAT\Property(property: 'created_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                                ]
                            )),
                            new OAT\Property(property: 'user2', type: 'array', items: new OAT\Items(
                                properties: [
                                    new OAT\Property(property: 'id', type: 'int', format: 'int', example: '1'),
                                    new OAT\Property(property: 'name', type: 'string', format: 'name', example: 'Oleg'),
                                    new OAT\Property(property: 'email', type: 'string', format: 'email', example: '11@mail.ru'),
                                    new OAT\Property(property: 'created_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                                ]
                            )),
                            new OAT\Property(property: 'latest_message', type: 'array', items: new OAT\Items(
                                properties: [
                                    new OAT\Property(property: 'id', type: 'int', format: 'int', example: '1'),
                                    new OAT\Property(property: 'message', type: 'string', format: 'string', example: 'shsga4rg34'),
                                    new OAT\Property(property: 'user_id', type: 'int', format: 'int', example: '2'),
                                    new OAT\Property(property: 'chat_id', type: 'int', format: 'int', example: '22'),
                                    new OAT\Property(property: 'viewed_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                                    new OAT\Property(property: 'created_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                                ]
                            ))
                        ]
                    ))
                ])
            ),
        ],
        parameters: [
            new OAT\Parameter(name: 'page', parameter: 'page', description: 'Текущая страница', in: 'query', required: false, deprecated: false, allowEmptyValue: true),
        ],
    )]
    function index(Request $request){
        $page = $request->input('page');
        $user_id = $request->user()->id;

        return self::response(self::getOrderChatsWithLastMessage($user_id, $page));
    }

    #[OAT\Get(
        path: '/chat/{id}',
        summary: 'Получение одного чата пользователя',
        description: 'Получение одного чата пользователя',
        tags: ['chat'],
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
                            new OAT\Property(property: 'user_id1', type: 'int', format: 'int', example: '23'),
                            new OAT\Property(property: 'user_id2', type: 'int', format: 'int', example: '11'),
                            new OAT\Property(property: 'created_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                        ]
                    ))
                ])
            ),
        ],
    )]
    function show(string $id){
        return self::response(Chat::with(['User1', 'User2'])->where('id', $id)->get());
    }
    

    #[OAT\Post(
        path: '/chat',
        summary: 'Создание чата для пользователя',
        description: 'Создание чата для пользователя',
        tags: ['chat'],
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
                            new OAT\Property(property: 'user_id1', type: 'int', format: 'int', example: '23'),
                            new OAT\Property(property: 'user_id2', type: 'int', format: 'int', example: '11'),
                            new OAT\Property(property: 'created_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                        ]
                    ))
                ])
            ),
        ],
        parameters: [new OAT\RequestBody(ref: "#/components/requestBodies/ChatCreateRequest")]
    )]
    function store(ChatCreateRequest $request){
        $user_id = $request->user()->id;
        
        $chat = new Chat;
        $message = new Message;

        DB::transaction(function() use ($request, $user_id, &$chat, &$message) {
            
            $chat->user_id1 = $user_id;
            $chat->user_id2 = $request->input('user_id2');

            $chat->save();

            $message->message = $request->input('message');
            $message->chat_id = $chat->id;
            $message->user_id = $user_id;

            $message->save();

            $chat = self::getChatWithLastMessage($message->chat_id);
            event(new UpdateChats($chat));
        });

        return self::response($chat);
    }
    
    #[OAT\Delete(
        path: '/chat/{id}',
        summary: 'Удаление чата пользователя',
        description: 'Удаление чата пользователя',
        tags: ['chat'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Успешно',
            ),
        ],
        parameters: [
            new OAT\Parameter(name: 'id', parameter: 'id', description: 'Идентификатор чата', in: 'query', required: true, deprecated: false, allowEmptyValue: true),
        ],
    )]
    function destroy(Chat $chat){ 
        DB::transaction(function() use ($chat) {
            Message::where('chat_id', $chat->id)->delete();
            
            $flag = $chat->delete();

            if($flag == 1) return self::response(statusCode: 200);
            else return self::response(statusCode: 500);
        });
    }

    /**
     * Функция, возвращающая чат, вкотором было последнее сообщение
     * 
     * @param int $chat_id индентификатор чата
     */
    private static function getChatWithLastMessage(int $chat_id)
    {
        return Chat::with(['User1', 'User2', 'latestMessage'])->
        where('chats.id', $chat_id)->get()->toArray()[0];
    }


    /**
     * Функция, возвращающая упорядоченный список чатов с последним сообщением
     * 
     * @param int $user_id индентификатор пользователя
     * @param int $param страница пагинациии
     */
    private static function getOrderChatsWithLastMessage(int $user_id, int $page)
    {
        $chats = Chat::selectRaw('
        chats.id chat_id, 
        chats.user_id1,
        chats.user_id2,
        chats.created_at created_at_chats,

        user1.name name_user1,
        user1.email email_user1,
        user1.created_at created_at_user1,

        user2.name name_user2,
        user2.email email_user2,
        user2.created_at created_at_user2,
        (select ROW_TO_JSON(messages) from messages where messages.chat_id = chats.id order by id desc limit 1) as latest_message
        ')->
        join('users as user1', 'user1.id','=', 'chats.user_id1')->
        join('users as user2', 'user2.id','=', 'chats.user_id2')->
        where('chats.user_id1', $user_id)->
        orWhere('chats.user_id2', $user_id)->
        orderBy(
            Message::select('id')->
            whereColumn('chats.id', 'messages.chat_id')->orderBy('messages.id', 'desc')->take(1), 
            'desc'
        );
        
        $chatsPaginate = $chats->paginate(perPage: env('PER_PAGE'), page: $page);
        ChatResource::collection($chatsPaginate);
        return $chatsPaginate;
    }
}