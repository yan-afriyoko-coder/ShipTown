<?php


namespace App\Modules\ScurriAnpost\src\Api;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;

class Consignment
{
    private Response $response;

    /**
     * @var array
     */
    public array $body;

    /**
     * @var Collection
     */
    public Collection $errors;

    /**
     * @var Collection
     */
    public Collection $success;

    public function __construct(Response $response)
    {
        $this->response = $response;
        $this->body = $response->json();

        $this->errors = collect($this->body['errors']);
        $this->success = collect($this->body['success']);
    }

    public function consignmentId()
    {
        return $this->response->json()['success'][0];
    }
}
