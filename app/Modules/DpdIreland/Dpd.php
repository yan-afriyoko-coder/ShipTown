<?php


namespace App\Modules\DpdIreland;

use App\Modules\DpdIreland\src\Client;
use App\Modules\DpdIreland\src\Responses\PreAdvice;
use App\Modules\DpdIreland\src\Consignment;
use Illuminate\Support\Facades\Log;
use Psy\Util\Str;

/**
 * Class Dpd
 * @package App\Modules\Dpd\src
 */
class Dpd
{
    /**
     * @param Consignment $consignment
     * @return PreAdvice
     */
    public static function getPreAdvice(Consignment $consignment): PreAdvice
    {
        $consignmentXml = $consignment->toXml();

        $response = Client::postXml($consignmentXml);

        $preAdvice = new PreAdvice($response->getBody()->getContents());

        if($preAdvice->isNotSuccess()) {
            Log::error('DPD PreAdvice request failed', [
                'xml_received' => $preAdvice->toString(),
                'xml_sent' => $consignment->toString(),
            ]);
        }

        return $preAdvice;
    }
}
