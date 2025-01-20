<?php
namespace App\Http\Source;

use Illuminate\Pagination\LengthAwarePaginator;
use OpenApi\Attributes as OAT;

trait Response{
    /**
     * Список возвращаемых заголовков
     */
    static private $headers = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Headers' => '*',
        'Access-Control-Allow-Credentials' => 'true',
    ];

    #[OAT\Schema(
        schema: 'Paginator',
        properties: [
            new OAT\Property(property: 'current_page', type: 'int', format: 'int', example: '1'),
            new OAT\Property(property: 'first_page_url', type: 'string', format: 'url', example: 'http://138.124.55.208/api/prod/chat?page=1'),
            new OAT\Property(property: 'from', type: 'int', format: 'int', example: '1'),
            new OAT\Property(property: 'last_page', type: 'int', format: 'int', example: '380'),
            new OAT\Property(property: 'last_page_url', type: 'string', format: 'url', example: 'http://138.124.55.208/api/prod/chat?page=5'),
            new OAT\Property(property: 'next_page_url', type: 'string', format: 'url', example: 'http://138.124.55.208/api/prod/chat?page=3'),
            new OAT\Property(property: 'path', type: 'string', format: 'url', example: 'http://138.124.55.208/api/prod/chat?page=2'),
            new OAT\Property(property: 'per_page', type: 'int', format: 'int', example: '30'),
            new OAT\Property(property: 'to', type: 'int', format: 'int', example: '30'),
            new OAT\Property(property: 'total', type: 'int', format: 'int', example: '11371'),
        ]
    )]
    static function toPaginator($response, $status)
    {
        return array_map(
            fn ($value) => [
                'status'=>$value['status'],
                'paginator'=>[
                    'current_page'=>$value['current_page'],
                    'first_page_url'=>$value['first_page_url'],
                    'from'=>$value['from'],
                    'last_page'=>$value['last_page'],
                    'last_page_url'=>$value['last_page_url'],
                    'next_page_url'=>$value['next_page_url'],
                    'path'=>$value['path'],
                    'per_page'=>$value['per_page'],
                    'to'=>$value['to'],
                    'total'=>$value['total'],

                ],
                'data'=>$value['data'],
            ], 
            [[ 'status' => $status ] + $response->toArray()]
        )[0];
    }

    /**
     * Функция, формирующая ответ
     * 
     * @param $response ответ
     * @param $statusCode статус код ответа
     */
    static function response($response = null, $statusCode = 200)
    {
        $status = (in_array($statusCode, [200, 201, 304]) ? 'Successfully' : 'Error');

        if(gettype($statusCode) == 'string') $statusCode = 500;
        
        if($response instanceof LengthAwarePaginator){
            return response(self::toPaginator($response, $status))->setStatusCode($statusCode)->withHeaders(self::$headers);
        }
        elseif (is_null($response)) return response()->noContent()->setStatusCode($statusCode); 
        else return response([
            'status' => $status,
            'data' => $response,

        ])->setStatusCode($statusCode)->withHeaders(self::$headers);
    }
}