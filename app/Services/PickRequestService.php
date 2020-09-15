<?php


namespace App\Services;

use App\Models\PickRequest;

/**
 * Class PickRequestService
 * @package App\Services
 */
class PickRequestService
{
    /**
     * @param PickRequest $pickRequest
     */
    public static function removeFromPicklist(PickRequest $pickRequest)
    {
        $pickRequest->pick()->decrement('quantity_required', $pickRequest->quantity_required);

        $pickRequest->update(['pick_id' => null]);
    }
}
