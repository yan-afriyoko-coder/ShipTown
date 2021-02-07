<?php


namespace App\Modules\Dpd\src;

use App\Modules\Dpd\src\Responses\PreAdvice;

/**
 * Class Dpd
 * @package App\Modules\Dpd\src
 */
class Dpd
{
    /**
     * @param array $shipment
     * @return PreAdvice
     */
    public static function getPreAdvice(array $shipment): PreAdvice
    {
        $shipment = new Shipment($shipment);

        $response = Client::postXml($shipment->toXml());

        return new PreAdvice($response->getBody()->getContents());
    }
}
