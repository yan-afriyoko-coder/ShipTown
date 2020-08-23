<?php


namespace App\Modules\Rmsapi\src;

use App\Models\RmsapiConnection;
use GuzzleHttp\Client as GuzzleClient;

/**
 * Class Client
 * @package App\Modules\Rmsapi\src
 */
class Client
{
    /**
     * @param RmsapiConnection $connection
     * @param string $uri
     * @param array $query
     * @return RequestResponse
     */
    public static function GET(RmsapiConnection $connection, string $uri, array $query = [])
    {
        $response = new RequestResponse(
            self::getGuzzleClient($connection)->get($uri, ['query' => $query])
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
     * @param RmsapiConnection $connection
     * @param string $uri
     * @param array $data
     * @return RequestResponse
     */
    public static function POST(RmsapiConnection $connection, string $uri, array $data)
    {
        $response = new RequestResponse(
            self::getGuzzleClient($connection)->post($uri, [
                'json' => $data
            ])
        );

        logger("POST", [
            "uri" => $uri,
            "json" => $data,
            "response" => [
                "status_code" => $response->getResponseRaw()->getStatusCode(),
                "body" => $response->asArray()
            ]
        ]);

        return $response;
    }

    /**
     * @param RmsapiConnection $connection
     * @param string $uri
     * @param array $query
     * @return RequestResponse
     */
    public static function DELETE(RmsapiConnection $connection, string $uri, array $query)
    {
        $response =  self::getGuzzleClient($connection)->delete($uri, ['query' => $query]);

        return new RequestResponse($response);
    }

    /**
     * @param RmsapiConnection $connection
     * @return GuzzleClient
     */
    public static function getGuzzleClient(RmsapiConnection $connection)
    {
        return new GuzzleClient([
            'base_uri' => $connection->url,
            'timeout' => 600,
            'exceptions' => false,
            'auth' => [
                $connection->username,
                \Crypt::decryptString($connection->password)
            ]
        ]);
    }
}
