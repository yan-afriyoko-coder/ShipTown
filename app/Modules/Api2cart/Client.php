<?php


namespace App\Modules\Api2cart;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    /**
     * @param string $store_key
     * @param string $uri
     * @param array $params
     * @return RequestResponse
     */
    static function GET(string $store_key, string $uri, array $params)
    {
        $query = [
            'api_key' => self::getApiKey(),
            'store_key' => $store_key
        ];

        $query = array_merge($query, $params);

        $response = new RequestResponse(
            self::getGuzzleClient()->get($uri, ['query' => $query])
        );

        logger("GET", [
           "uri" => $uri,
           "query" => $query,
           "response" => [
                "status_code" => $response->getResponseRaw()->getStatusCode(),
                "body" => $response->asArray()
           ]
        ]);

        return $response;
    }

    /**
     * @param string $store_key
     * @param string $uri
     * @param array $data
     * @return RequestResponse
     */
    static function POST(string $store_key, string $uri, array $data)
    {
        $query = [
            'api_key' => self::getApiKey(),
            'store_key' => $store_key
        ];

        $response = new RequestResponse(
            self::getGuzzleClient()->post($uri, [
                'query' => $query,
                'json' => $data
            ])
        );

        logger("POST", [
            "uri" => $uri,
            "query" => $query,
            "json" => $data,
            "response" => [
                "status_code" => $response->getResponseRaw()->getStatusCode(),
                "body" => $response->asArray()
            ]
        ]);

        return $response;
    }

    /**
     * @param string $store_key
     * @param string $uri
     * @param array $params
     * @return RequestResponse
     */
    static function DELETE(string $store_key, string $uri, array $params)
    {
        $query = [
            'api_key' => self::getApiKey(),
            'store_key' => $store_key
        ];

        $query = array_merge($query, $params);

        $response =  self::getGuzzleClient()->delete($uri, ['query' => $query]);

        return new RequestResponse($response);
    }

    static function getGuzzleClient()
    {
        return new GuzzleClient([
            'base_uri' =>  'https://api.api2cart.com/v1.1/',
            'timeout' => 60,
            'exceptions' => true,
        ]);
    }

    static function getApiKey()
    {
        return config('app.api2cart_api_key');
    }

}
