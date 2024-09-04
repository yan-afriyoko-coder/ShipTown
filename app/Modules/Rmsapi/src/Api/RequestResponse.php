<?php

namespace App\Modules\Rmsapi\src\Api;

use Psr\Http\Message\ResponseInterface;

class RequestResponse
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var string
     */
    private $response_content;

    /**
     * Api2CartResponse constructor.
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;

        // used casting because PSR-7
        // https://stackoverflow.com/questions/30549226/guzzlehttp-how-get-the-body-of-a-response-from-guzzle-6
        $this->response_content = $response->getBody()->getContents();
    }

    /**
     * @return string
     */
    public function getAsJson()
    {
        return $this->response_content;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponseRaw()
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->response->getStatusCode() == 200;
    }

    /**
     * @return bool
     */
    public function isNotSuccess()
    {
        return ! $this->isSuccess();
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return json_decode($this->response_content, true);
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->asArray()['data'];
    }
}
