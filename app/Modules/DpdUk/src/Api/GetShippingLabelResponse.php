<?php

namespace App\Modules\DpdUk\src\Api;

use Illuminate\Http\Client\Response;

/**
 *
 */
class GetShippingLabelResponse
{
    /**
     * @var Response
     */
    public Response $response;

    /**
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }
}
