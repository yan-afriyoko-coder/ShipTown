<?php

namespace App\Modules\DpdUk\src\Api;

use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;

/**
 *
 */
class ApiResponse
{
    /**
     * @var ResponseInterface
     */
    public ResponseInterface $http_response;
    /**
     * @var string
     */
    public string $content;

    /**
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->http_response = $response;
        $this->content = $response->getBody()->getContents();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return json_decode($this->content, true);
    }

    /**
     * @return Collection
     */
    public function toCollection(): Collection
    {
        return collect($this->toArray());
    }
}
