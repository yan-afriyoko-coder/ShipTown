<?php

namespace App\Modules\DpdUk\src\Api;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class HttpClient
{
    private GuzzleClient $guzzleClient;

    public function __construct(array $options = [])
    {
        $this->guzzleClient = new GuzzleClient($options);
    }

    /**
     * @throws GuzzleException
     */
    public function request(string $method, $uri = '', array $options = []): ResponseInterface
    {
        ray($method, $uri, $options);
        return $this->guzzleClient->request($method, $uri, $options);
    }
}
