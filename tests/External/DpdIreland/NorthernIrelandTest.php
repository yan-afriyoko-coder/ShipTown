<?php

namespace Tests\External\DpdIreland;

use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Consignment;
use App\Modules\DpdIreland\src\Responses\PreAdvice;
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
     * @test
     */
    public function consignment_with_one_parcel()
    {
        $this->markTestIncomplete('Request fails, database error');

        $consignment = new Consignment([
            'ServiceType' => Consignment::SERVICE_TYPE_2_DAY_SERVICE,
            'DeliveryAddress' => [
                'Contact' => 'John Smith',
                'ContactTelephone' => '00443333040191',
                'ContactEmail' => 'john.smith@ie.ie',
                'BusinessName' => 'JS Business',
                'AddressLine1' => 'Unit 11a, Boucher Shopping Park',
                'AddressLine2' => 'Boucher Crescent',
                'AddressLine3' => 'Belfast',
                'AddressLine4' => 'BT12 6HU',
                'CountryCode' =>  'UK',
            ],
            'CollectionAddress' => [
                'Contact' => 'John Smith',
                'ContactTelephone' => '12345678901',
                'ContactEmail' => 'john.smith@ie.ie',
                'BusinessName' => 'JS Business',
                'AddressLine1' => 'DPD Ireland, Westmeath',
                'AddressLine2' => 'Unit 2B Midland Gateway Bus',
                'AddressLine3' => 'Kilbeggan',
                'AddressLine4' => 'Westmeath',
                'CountryCode' =>  'IE',
            ],
        ]);

        $preAdvice = Dpd::getPreAdvice($consignment);

        $this->assertTrue($preAdvice->responseIsSuccess());
    }
}
