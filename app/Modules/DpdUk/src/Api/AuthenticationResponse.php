<?php

namespace App\Modules\DpdUk\src\Api;

/**
 *
 */
class AuthenticationResponse
{
    /**
     * @var ApiResponse
     */
    public ApiResponse $response;

    /**
     * @param ApiResponse $response
     */
    public function __construct(ApiResponse $response)
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getGeoSession(): string
    {
        return $this->response->toArray()['data']['geoSession'];
    }
}
