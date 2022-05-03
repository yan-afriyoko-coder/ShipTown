<?php

namespace App\Modules\DpdUk\src\Api;

use App\Modules\DpdUk\src\Models\Connection;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 *
 */
class ApiClient
{
    /**
     * Cache key name used for caching authorization.
     */
    const GEO_SESSION_CACHE_KEY_NAME = 'dpd-uk.geo-session';

    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @var GuzzleClient
     */
    private GuzzleClient $guzzleClient;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->guzzleClient = new GuzzleClient([
            'base_uri' => 'https://api.dpd.co.uk/',
            'http_errors' => false,
        ]);
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getGeoSession(): string
    {
        $geoSession = Cache::get(self::GEO_SESSION_CACHE_KEY_NAME);

        if ($geoSession) {
            return $geoSession;
        }

        $authenticationResponse = $this->postAuthenticationsRequest();

        if ($authenticationResponse->response->http_response->getStatusCode() !== 200) {
            throw new Exception('DPD UK Authentication failed: ' .
                $authenticationResponse->response->http_response->getStatusCode() .
                ' ' .
                $authenticationResponse->response->content);
        }

        $geoSession = $authenticationResponse->getGeoSession();

        // save to cache
        Cache::put(self::GEO_SESSION_CACHE_KEY_NAME, $geoSession, 86400);

        return $geoSession;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return ApiResponse|null
     * @throws Exception
     */
    public function request(string $method, string $uri = '', array $options = []): ?ApiResponse
    {
        if (! isset($options['headers']['GeoSession'])) {
            $options['headers']['GeoSession'] = $this->getGeoSession();
        }

        try {
            $response = new ApiResponse($this->guzzleClient->request($method, $uri, $options));

            Log::debug('API REQUEST', [
                'service' => 'DPD-UK',
                'response' => [
                    'status_code' => $response->http_response->getStatusCode(),
                    'message' => Str::substr($response->content, 0, 1024)
                ],
                'request' => [
                    'method' => $method,
                    'uri' => $uri,
                    'options' => $options
                ],
            ]);

            return $response;
        } catch (GuzzleException $guzzleException) {
            Log::error($guzzleException->getMessage(), [$guzzleException->getCode()]);
            return null;
        }
    }

    /**
     * @param array $payload
     * @return CreateShipmentResponse
     * @throws Exception
     */
    public function createShipment(array $payload): CreateShipmentResponse
    {
        $payload['consignment'][0]['networkCode'] = $this->getNetworkCode($payload);

        $shipmentResponse = new CreateShipmentResponse(
            $this->request('POST', 'shipping/shipment', [
                'json' => $payload
            ])
        );

        if ($shipmentResponse->errors()) {
            $shipmentResponse->errors()->each(function ($error) {
                throw new Exception(
                    $error['obj'] . ': ' . $error['errorMessage'],
                    $error['errorCode']
                );
            });
        }

        return $shipmentResponse;
    }

    /**
     * @param int $shipmentId
     * @return GetShippingLabelResponse
     * @throws Exception
     */
    public function getShipmentLabel(int $shipmentId): GetShippingLabelResponse
    {
        return new GetShippingLabelResponse(
            $this->request('GET', '/shipping/shipment/' . $shipmentId . '/label/', [
                'headers' => [
                    'Accept' => 'text/vnd.eltron-epl'
                ]
            ])
        );
    }

    /**
     * @return AuthenticationResponse
     * @throws Exception
     */
    private function postAuthenticationsRequest(): AuthenticationResponse
    {
        if ($this->connection->username === '') {
            throw new Exception('DPD UK: "username" not set');
        }

        if ($this->connection->password === '') {
            throw new Exception('DPD UK: "password" not set');
        }

        if ($this->connection->account_number === '') {
            throw new Exception('DPD UK: "account_number" not set');
        }

        return new AuthenticationResponse(
            $this->request('POST', "user/?action=login", [
                'auth' => [
                    $this->connection->username,
                    $this->connection->password,
                ],
                'headers' => [
                    'GeoClient' => 'account/' . $this->connection->account_number,
                    'GeoSession' => '',
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ])
        );
    }

    /**
     * @param array $payload
     * @return string
     * @throws Exception
     */
    private function getNetworkCode(array $payload): string
    {
        $query = Arr::dot($payload['consignment'][0]);

        $this->request('GET', "shipping/network", [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'query' => $query,
        ]);

        return "1^12";
    }
}
