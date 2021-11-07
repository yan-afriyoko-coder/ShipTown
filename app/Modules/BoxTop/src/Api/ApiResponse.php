<?php

namespace App\Modules\BoxTop\src\Api;

use Psr\Http\Message\ResponseInterface;

class ApiResponse
{
    public ResponseInterface $http_response;
    public string $content;

    public function __construct(ResponseInterface $response)
    {
        $this->http_response = $response;
        $this->content = $response->getBody()->getContents();
    }
}
