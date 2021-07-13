<?php


namespace App\Modules\ScurriAnpost\src\Api;

use Illuminate\Http\Client\Response;

/**
 * Class Label
 * @package App\Modules\ScurriAnpost\src\Api
 */
class Label
{
    /**
     * @var Response
     */
    public Response $response;

    /**
     * @var array
     */
    public array $body;

    /**
     * Label constructor.
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
        $this->body = $response->json();
    }

    /**
     * @return string|null
     */
    public function getLabels(): ?string
    {
        return base64_decode($this->body['labels']);
    }
}
