<?php

namespace App\Modules\DpdUk\src\Api;

use Illuminate\Support\Collection;

/**
 *
 */
class CreateShipmentResponse
{
    /**
     * @var ApiResponse
     */
    private ApiResponse $apiResponse;

    /**
     * @param ApiResponse $response
     */
    public function __construct(ApiResponse $response)
    {
        $this->apiResponse = $response;
    }

    /**
     * @return mixed
     */
    public function getShipmentId()
    {
        return $this->apiResponse->toArray()['data']['shipmentId'];
    }

    /**
     * @return Collection
     */
    public function errors(): Collection
    {
        return collect($this->apiResponse->toArray()['error']);
    }

    /**
     * @return mixed
     */
    public function getConsignmentNumber()
    {
        return $this->apiResponse->toArray()['data']['consignmentDetail'][0]['consignmentNumber'];
    }

    /**
     * @return mixed
     */
    public function getConsignmentParcelNumber()
    {
        return $this->apiResponse->toArray()['data']['consignmentDetail'][0]['parcelNumbers'][0]['parcelNumbers'];
    }
}
