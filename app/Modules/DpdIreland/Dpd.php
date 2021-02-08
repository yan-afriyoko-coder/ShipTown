<?php


namespace App\Modules\DpdIreland;

use App\Modules\DpdIreland\src\Client;
use App\Modules\DpdIreland\src\Responses\PreAdvice;
use App\Modules\DpdIreland\src\Shipment;

/**
 * Class Dpd
 * @package App\Modules\Dpd\src
 */
class Dpd
{
    /**
     * @param Shipment $shipment
     * @return PreAdvice
     */
    public static function getPreAdvice(Shipment $shipment): PreAdvice
    {
        $response = Client::postXml($shipment->toXml());

        return new PreAdvice($response->getBody()->getContents());
    }
}
