<?php

namespace App\Modules\DpdUk\src\Models;

use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;

class CreateShipmentResponse
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

    public function getShipmentId()
    {
        return $this->content['data']['shipmentId'];
    }

    public function errors(): Collection
    {
        return collect($this->content['error']);
    }
}
