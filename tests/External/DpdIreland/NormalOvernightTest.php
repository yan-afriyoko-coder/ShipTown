<?php


namespace Tests\External\DpdIreland;


use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Consignment;

class NormalOvernightTest
{
    /**
     * @test
     */
    public function normal_overnight_consignment()
    {
        $consignment = new Consignment([
            'DeliveryAddress' => [
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

        $this->assertEquals($consignment->toArray()['RecordID'], $preAdvice->getAttribute('RecordID'));
    }
}
