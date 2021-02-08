<?php


namespace Tests\External\DpdIreland\TestCases;

use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Consignment;

/**
 * Customer sending to Northern Ireland from
 * a ROI collection address using service type
 * “Overnight” with service option “Normal”
 *
 * Class NorthernIrelandTest
 * @package Tests\External\DpdIreland\TestCases
 */
class NorthernIrelandTest
{
    /**
     * One consignment with 1 parcel
     */
    public function test_consignment_with_one_parcel()
    {
        $consignment = new Consignment([
            'DeliveryAddress' => [

            ]
        ]);

        Dpd::getPreAdvice($consignment);

        $this->assertEquals('OK', $preAdvice->isSucess());
    }
}
