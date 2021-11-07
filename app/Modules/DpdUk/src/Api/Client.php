<?php

namespace App\Modules\DpdUk\src\Api;

use App\Modules\DpdUk\src\Models\AuthenticationResponse;
use App\Modules\DpdUk\src\Models\Connection;
use App\Modules\DpdUk\src\Models\CreateShipmentResponse;
use App\Modules\DpdUk\src\Models\GetShippingLabelResponse;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Log;
use Psr\Http\Message\ResponseInterface;

class Client
{
    private Connection $connection;

    private HttpClient $httpClient;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->httpClient = new HttpClient(['base_uri' => 'https://api.dpd.co.uk/']);
    }

    /**
     * @throws GuzzleException
     */
    public function authenticate(): AuthenticationResponse
    {
        $authenticationResponse = $this->postAuthenticationsRequest();

        $this->connection->geo_session = $authenticationResponse->getGeoSession();
        $this->connection->save();

        return $authenticationResponse;
    }

    /**
     * @throws GuzzleException
     */
    public function request(string $method, $uri = '', array $options = []): ResponseInterface
    {
        if ($this->connection->geo_session === null) {
            $this->authenticate();
        }

        Log::debug('DPDUK HTTP REQUEST', ['method' => $method, 'uri' => $uri, 'options' => $options]);
        return $this->httpClient->request($method, $uri, $options);
    }

    /**
     * @param array $payload
     * @return CreateShipmentResponse
     * @throws GuzzleException
     * @throws Exception
     */
    public function createShipment(array $payload): CreateShipmentResponse
    {
        $payload['consignment'][0]['networkCode'] = $this->getNetworkCode($payload);

        $createShipmentResponse = new CreateShipmentResponse(
            $this->request('POST', 'shipping/shipment', [
                'headers' => [
                    'GeoSession' => $this->getGeoSession()
                ],
                'json' => $payload
            ])
        );

        Log::debug('DPD UK create.shipment', ['payload' => $payload, 'response' => $createShipmentResponse->content]);

        return $createShipmentResponse;
    }

    /**
     * @param int $shipmentId
     * @return GetShippingLabelResponse
     * @throws GuzzleException
     */
    public function getShipmentLabel(int $shipmentId): GetShippingLabelResponse
    {
        return new GetShippingLabelResponse(
            $this->request('GET', '/shipping/shipment/' . $shipmentId . '/label/', [
                'headers' => [
                    'GeoSession' => $this->getGeoSession(),
                    'Accept' => 'text/vnd.eltron-epl'
                ]
            ])
        );
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    private function getGeoSession(): string
    {
        if ($this->connection->geo_session == null) {
            $this->authenticate();
        }

        return $this->connection->geo_session;
    }

    /**
     * @return AuthenticationResponse
     * @throws GuzzleException
     */
    private function postAuthenticationsRequest(): AuthenticationResponse
    {
        return new AuthenticationResponse(
            $this->httpClient->request('POST', "user/?action=login", [
                'auth' => [
                    $this->connection->username,
                    $this->connection->password,
                ],
                'headers' => [
                    'GeoClient' => 'account/' . $this->connection->account_number,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ])
        );
    }

    /**
     * @param array $payload
     * @return string
     * @throws GuzzleException
     */
    private function getNetworkCode(array $payload): string
    {
        $query = Arr::dot($payload['consignment'][0]);

        $response = $this->httpClient->request('GET', "shipping/network", [
            'headers' => [
                'GeoSession' => $this->getGeoSession(),
                'Accept' => 'application/json'
            ],
            'query' => $query,
        ]);

        return "1^12";
    }
}
