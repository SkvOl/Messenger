<?php
namespace App\Http\Entities\Message\Controllers;

use App\Http\Entities\Message\Requests\MessageChangeRequest;
use App\Http\Entities\Message\Requests\MessageCreateRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Source\Controller;
use OpenApi\Attributes as OAT;
use App\Http\Source\Response;
use App\Events\CreateMessage;
use App\Events\ChangeMessage;
use App\Events\DeleteMessage;
use App\Events\WatchMessage;
use Illuminate\Http\Request;
use App\Events\UpdateChats;
use App\Models\Message;
use App\Models\Chat;

class MessageController extends Controller{
    use Response;

    #[OAT\Get(
        path: '/message',
        summary: 'Получение списка сообщений пользователя',
        description: 'Получение списка сообщений пользователя',
        tags: ['message'],
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
                            new OAT\Property(property: 'message', type: 'string', format: 'string', example: 'shsga4rg34'),
                            new OAT\Property(property: 'user_id', type: 'int', format: 'int', example: '2'),
                            new OAT\Property(property: 'chat_id', type: 'int', format: 'int', example: '22'),
                            new OAT\Property(property: 'viewed_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                            new OAT\Property(property: 'created_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                            new OAT\Property(property: 'updated_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                        ]
                    ))
                ])
            ),
        ],
        parameters: [
            new OAT\Parameter(name: 'chat_id', parameter: 'chat_id', description: 'Идентификатор чата', in: 'query', required: true, deprecated: false, allowEmptyValue: true),
            new OAT\Parameter(name: 'last_id', parameter: 'last_id', description: 'Идентифиувтор последнего отправленного серверу сообщения', in: 'query', required: true, deprecated: false, allowEmptyValue: true),
        ],
    )]
    function index(Request $request){
        $chat_id = $request->input('chat_id');
        $last_id = $request->input('last_id');
        $user_id = $request->user()->id;
        
        $message = Message::select('messages.*')->join('chats', 'chats.id', '=', 'messages.chat_id')->
        where('messages.chat_id', $chat_id)->
        where(function($query) use ($user_id) {
            $query->where('chats.user_id1', $user_id)->orWhere('chats.user_id2', $user_id);
        });
        
        
        if(isset($last_id)){
            $message = $message->where('messages.id', '<', $last_id);
        }

        return self::response($message->orderBy('messages.id', 'desc')->take(20)->get());
    }
    
    #[OAT\Get(
        path: '/message/{id}',
        summary: 'Получение одного сообщения пользователя',
        description: 'Получение одного сообщения пользователя',
        tags: ['message'],
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
                            new OAT\Property(property: 'message', type: 'string', format: 'string', example: 'shsga4rg34'),
                            new OAT\Property(property: 'user_id', type: 'int', format: 'int', example: '2'),
                            new OAT\Property(property: 'chat_id', type: 'int', format: 'int', example: '22'),
                            new OAT\Property(property: 'viewed_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                            new OAT\Property(property: 'created_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                            new OAT\Property(property: 'updated_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                        ]
                    ))
                ])
            ),
        ],
    )]
    function show(Message $message){
        return self::response($message->toArray());
    }

    #[OAT\Post(
        path: '/message',
        summary: 'Создание сообщения',
        description: 'Создание сообщения',
        tags: ['message'],
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
                        ]
                    ))
                ])
            ),
        ],
        parameters: [new OAT\RequestBody(ref: "#/components/requestBodies/MessageCreateRequest")]
    )]
    function store(MessageCreateRequest $request){
        $message = new Message;
        
        DB::transaction(function() use ($request, &$message) {
            $message->message = $request->message;
            $message->chat_id = $request->chat_id;
            $message->user_id = $request->user()->id;

            $message->save();
            
            event(new CreateMessage($message));

            $chat = self::getChatWithLastMessage($message->chat_id);
            event(new UpdateChats($chat));
        });

        return self::response([
            'id'=>$message->id,
        ]);
    }

    #[OAT\Patch(
        path: '/message/{id}',
        summary: 'Изменение сообщения',
        description: 'Изменение сообщения',
        tags: ['message'],
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
                        ]
                    ))
                ])
            ),
        ],
        parameters: [new OAT\RequestBody(ref: "#/components/requestBodies/MessageChangeRequest")]
    )]
    function update(MessageChangeRequest $request, Message $message){
        if($message->user_id == $request->user()->id)
        DB::transaction(function() use ($request, $message) {
            $message->message = $request->message;
            $message->save();

            event(new ChangeMessage($message->toArray()));

            $chat = self::getChatWithLastMessage($message->chat_id);
            event(new UpdateChats($chat));
        });

        return self::response([
            'id'=>$message->id,
        ]);
    }
    
    #[OAT\Delete(
        path: '/message',
        summary: 'Удаление сообщения',
        description: 'Удаление сообщения',
        tags: ['message'],
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
                        ]
                    ))
                ])
            ),
        ],
        parameters: [
            new OAT\Parameter(name: 'id', parameter: 'id', description: 'Идентификатор сообщения', in: 'query', required: true, deprecated: false, allowEmptyValue: true),
        ],
    )]
    function destroy(Message $message){ 
        DB::transaction(function() use ($message) {
            
            $flag = $message->delete();
            
            if($flag == 1) {
                event(new DeleteMessage($message->toArray()));

                if(self::getCountMessage($message->chat_id) == 0) Chat::where('id', $message->chat_id)->delete();
                else{
                    $chat = self::getChatWithLastMessage($message->chat_id);
                    event(new UpdateChats($chat));
                }

                return self::response(statusCode: 200);
            }
            else return self::response(statusCode: 500);
        }); 
    }

    #[OAT\Patch(
        path: '/message/watch/{message}',
        summary: 'Изменение статуса просмотра сообщения',
        description: 'Изменение статуса просмотра сообщения',
        tags: ['message'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Успешно',
                content: new OAT\JsonContent(properties: [
                    new OAT\Property(property: 'status', type: 'string', format: 'string', example: 'Successfully'),
                    new OAT\Property(property: 'paginator', ref: '#/components/schemas/Paginator'),
                    new OAT\Property(property: 'data', type: 'array', items: new OAT\Items(
                        properties: [
                            new OAT\Property(property: 'viewed_at', type: 'TIMESTAMP', format: 'TIMESTAMP', example: '2025-01-19 19:55:24'),
                        ]
                    ))
                ])
            ),
        ],
    )]
    function watch(Request $request, Message $message){
        $time = now();

        if($message->user_id != $request->user()->id)
        DB::transaction(function() use ($time, $message) {
                $message->viewed_at = $time;
                $message->save();

                event(new WatchMessage($message));

                $chat = self::getChatWithLastMessage($message->chat_id);
                event(new UpdateChats($chat));
        });

        return self::response([
            'viewed_at'=>$time
        ]);
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
     * Получение количества сообщений в чате
     * 
     * @param int $chat_id индентификатор чата
     */
    private function getCountMessage(int $chat_id): int
    {
        return Message::selectRaw('COUNT(id) as count')->where('chat_id', $chat_id)->get()[0]->count;
    }
}
