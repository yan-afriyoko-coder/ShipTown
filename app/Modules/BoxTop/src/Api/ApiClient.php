<?php

namespace App\Modules\BoxTop\src\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 *
 */
class ApiClient
{
    /**
     * @var Client
     */
    private Client $guzzleClient;

    /**
     *
     */
    public function __construct()
    {
        $this->guzzleClient = new Client(['base_uri' => 'https://api.boxtrax.com/api/']);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function request(string $method, string $uri = '', array $options = []): ResponseInterface
    {
        $options['headers']['BoxTopKey'] = env('TEST_BOXTOP_KEY');
        $options['headers']['Accept'] = 'application/json';
        $options['query']['custaccnum'] = env('TEST_BOXTOP_CUSTACCNUM');

        return $this->guzzleClient->request($method, $uri, $options);
    }

    /**
     * @return ApiResponse
     * @throws GuzzleException
     */
    public function getAllProducts(): ApiResponse
    {
        return new ApiResponse($this->request('GET', '513/Warehouse/getallproducts'));
    }
}
