<?php

namespace App\Modules\BoxTop\src\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Str;

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
        $this->guzzleClient = new Client([
            'base_uri' => 'https://api.boxtrax.com/',
            'http_errors' => false,
        ]);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return ApiResponse
     */
    public function request(string $method, string $uri = '', array $options = []): ApiResponse
    {
        $options['headers']['BoxTopKey'] = env('TEST_BOXTOP_KEY');
        $options['headers']['Accept'] = 'application/json';
        $options['query']['custaccnum'] = env('TEST_BOXTOP_CUSTACCNUM');

        $finalUri = str_replace('{siteCode}', env('TEST_BOXTOP_SITE_CODE', '000'), $uri);

        Log::debug('Boxtop API request', [
            'method' => $method,
            'uri' => $finalUri,
            'options' => $options,
        ]);

        try {
            $rawResponse = $this->guzzleClient->request($method, $finalUri, $options);

            $response = new ApiResponse($rawResponse);
        } catch (GuzzleException $guzzleException) {
            Log::error($guzzleException->getMessage(), [
                $guzzleException->getCode()
            ]);
        }

        Log::debug('API REQUEST', [
            'service' => 'BoxTop',
            'response' => [
                'status_code' => $response->http_response->getStatusCode(),
                'message' => Str::substr($response->content, 0, 1024)
            ],
            'request' => [
                'method' => $method,
                'uri' => $finalUri,
                'options' => $options
            ],
        ]);

        return $response;
    }

    /**
     * @return ApiResponse
     */
    public function getAllProducts(): ApiResponse
    {
        return $this->request('GET', 'api/{siteCode}/Warehouse/GetAllProducts');
    }

    /**
     */
    public function createWarehousePick(array $data): ApiResponse
    {
        return $this->request('POST', 'api/{siteCode}/Warehouse/CreateWarehousePick', [
            'query' => [
                'fullResponse' => 'true'
            ],
            'json' => $data
        ]);
    }

    /**
     * @param string $sku
     * @return ApiResponse
     */
    public function getSkuQuantity(string $sku): ApiResponse
    {
        return $this->request('GET', 'api/{siteCode}/Warehouse/GetSKUQuantity', [
            'query' => [
                'skuNumber' => $sku
            ]
        ]);
    }

    /**
     * @return ApiResponse
     */
    public function getStockCheckByWarehouse(): ApiResponse
    {
        return $this->request('GET', 'api/{siteCode}/Warehouse/GetStockCheckByWarehouse', []);
    }
}
