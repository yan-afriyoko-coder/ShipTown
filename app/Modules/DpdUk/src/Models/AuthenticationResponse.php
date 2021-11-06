<?php

namespace App\Modules\DpdUk\src\Models;

use Psr\Http\Message\ResponseInterface;

/**
 *
 */
class AuthenticationResponse
{
    /**
     * @var ResponseInterface
     */
    private ResponseInterface $response;
    /**
     * @var array
     */
    private array $content;

    /**
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
        $this->content = json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @return string
     */
    public function getGeoSession(): string
    {
        return $this->content['data']['geoSession'];
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
