<?php

namespace App\Modules\DpdUk\src\Api;

/**
 *
 */
class GetShippingLabelResponse
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
}
