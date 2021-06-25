<?php

namespace Tests\External\DpdIreland;

use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Consignment;
use Tests\TestCase;

class NormalOvernightTest extends TestCase
{
    use SeedConfiguration;

    /**
     * @test
     */
    public function normal_overnight_consignment_single_parcel()
    {
        $consignment = new Consignment([
            'DeliveryAddress' => [
                'Contact'          => 'John Smith',
                'ContactTelephone' => '12345678901',
                'ContactEmail'     => 'john.smith@ie.ie',
                'BusinessName'     => 'JS Business',
                'AddressLine1'     => 'DPD Ireland, Westmeath',
                'AddressLine2'     => 'Unit 2B Midland Gateway Bus',
                'AddressLine3'     => 'Kilbeggan',
                'AddressLine4'     => 'Westmeath',
                'CountryCode'      => 'IE',
            ],
            'CollectionAddress' => [
                'Contact'          => 'John Smith',
                'ContactTelephone' => '12345678901',
                'ContactEmail'     => 'john.smith@ie.ie',
                'BusinessName'     => 'JS Business',
                'AddressLine1'     => 'DPD Ireland, Westmeath',
                'AddressLine2'     => 'Unit 2B Midland Gateway Bus',
                'AddressLine3'     => 'Kilbeggan',
                'AddressLine4'     => 'Westmeath',
                'PostCode'         => 'XYZ1234',
                'CountryCode'      => 'IE',
            ],
        ]);

        $preAdvice = Dpd::getPreAdvice($consignment);

        $this->assertEquals($consignment->toArray()['RecordID'], $preAdvice->getAttribute('RecordID'));
    }

    /**
     * @test
     */
    public function normal_overnight_consignment_between_2_and_10_parcels()
    {
        $consignment = new Consignment([
            'TotalParcels'    => rand(2, 10),
            'DeliveryAddress' => [
                'Contact'          => 'John Smith',
                'ContactTelephone' => '12345678901',
                'ContactEmail'     => 'john.smith@ie.ie',
                'BusinessName'     => 'JS Business',
                'AddressLine1'     => 'DPD Ireland, Westmeath',
                'AddressLine2'     => 'Unit 2B Midland Gateway Bus',
                'AddressLine3'     => 'Kilbeggan',
                'AddressLine4'     => 'Westmeath',
                'CountryCode'      => 'IE',
            ],
            'CollectionAddress' => [
                'Contact'          => 'John Smith',
                'ContactTelephone' => '12345678901',
                'ContactEmail'     => 'john.smith@ie.ie',
                'BusinessName'     => 'JS Business',
                'AddressLine1'     => 'DPD Ireland, Westmeath',
                'AddressLine2'     => 'Unit 2B Midland Gateway Bus',
                'AddressLine3'     => 'Kilbeggan',
                'AddressLine4'     => 'Westmeath',
                'PostCode'         => 'XYZ1234',
                'CountryCode'      => 'IE',
            ],
        ]);

        $preAdvice = Dpd::getPreAdvice($consignment);

        $this->assertEquals($consignment->toArray()['RecordID'], $preAdvice->getAttribute('RecordID'));
    }

    /**
     * @test
     */
    public function normal_overnight_consignment_more_than_10_parcels()
    {
        $consignment = new Consignment([
            'TotalParcels'    => rand(11, 20),
            'DeliveryAddress' => [
                'Contact'          => 'John Smith',
                'ContactTelephone' => '12345678901',
                'ContactEmail'     => 'john.smith@ie.ie',
                'BusinessName'     => 'JS Business',
                'AddressLine1'     => 'DPD Ireland, Westmeath',
                'AddressLine2'     => 'Unit 2B Midland Gateway Bus',
                'AddressLine3'     => 'Kilbeggan',
                'AddressLine4'     => 'Westmeath',
                'CountryCode'      => 'IE',
            ],
            'CollectionAddress' => [
                'Contact'          => 'John Smith',
                'ContactTelephone' => '12345678901',
                'ContactEmail'     => 'john.smith@ie.ie',
                'BusinessName'     => 'JS Business',
                'AddressLine1'     => 'DPD Ireland, Westmeath',
                'AddressLine2'     => 'Unit 2B Midland Gateway Bus',
                'AddressLine3'     => 'Kilbeggan',
                'AddressLine4'     => 'Westmeath',
                'PostCode'         => 'XYZ1234',
                'CountryCode'      => 'IE',
            ],
        ]);

        $preAdvice = Dpd::getPreAdvice($consignment);

        $this->assertEquals($consignment->toArray()['RecordID'], $preAdvice->getAttribute('RecordID'));
    }

    /**
     * @test
     */
    public function if_succeeds_with_wrong_postcode()
    {
        $consignment = new Consignment([
            'TotalParcels'    => rand(11, 20),
            'DeliveryAddress' => [
                'Contact'          => 'John Smith',
                'ContactTelephone' => '12345678901',
                'ContactEmail'     => 'john.smith@ie.ie',
                'BusinessName'     => 'JS Business',
                'AddressLine1'     => 'DPD Ireland, Westmeath',
                'AddressLine2'     => 'Unit 2B Midland Gateway Bus',
                'AddressLine3'     => 'Kilbeggan',
                'AddressLine4'     => 'Westmeath',
                'PostCode'         => 'XYZ12345678901234',
                'CountryCode'      => 'IE',
            ],
            'CollectionAddress' => [
                'Contact'          => 'John Smith',
                'ContactTelephone' => '12345678901',
                'ContactEmail'     => 'john.smith@ie.ie',
                'BusinessName'     => 'JS Business',
                'AddressLine1'     => 'DPD Ireland, Westmeath',
                'AddressLine2'     => 'Unit 2B Midland Gateway Bus',
                'AddressLine3'     => 'Kilbeggan',
                'AddressLine4'     => 'Westmeath',
                'PostCode'         => 'XYZ1234',
                'CountryCode'      => 'IE',
            ],
        ]);

        $preAdvice = Dpd::getPreAdvice($consignment);

        $this->assertEquals($consignment->toArray()['RecordID'], $preAdvice->getAttribute('RecordID'));
    }
}
