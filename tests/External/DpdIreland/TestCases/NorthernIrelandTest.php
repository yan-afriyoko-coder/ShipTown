<?php


namespace Tests\External\DpdIreland\TestCases;

use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Consignment;
use Tests\TestCase;

/**
 * Customer sending to Northern Ireland from
 * a ROI collection address using service type
 * “Overnight” with service option “Normal”
 *
 * Class NorthernIrelandTest
 * @package Tests\External\DpdIreland\TestCases
 */
class NorthernIrelandTest extends TestCase
{
    /**
     * One consignment with 1 parcel
     */
    public function test_consignment_with_one_parcel_test()
    {
        dd(1);
        $consignment = new Consignment([
            'DeliveryAddress' => [

            ]
        ]);

        $preAdvice = Dpd::getPreAdvice($consignment);

        $this->assertTrue($preAdvice->isSuccess());
    }
}
