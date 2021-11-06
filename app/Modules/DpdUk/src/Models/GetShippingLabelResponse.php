<?php

namespace App\Modules\DpdUk\src\Models;

use Psr\Http\Message\ResponseInterface;

/**
 *
 */
class GetShippingLabelResponse
{
    /**
     * @var ResponseInterface
     */
    private ResponseInterface $response;

    /**
     * @var string
     */
    private string $content;

    /**
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
        $this->content = $response->getBody()->getContents();
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
