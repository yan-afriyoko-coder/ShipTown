<?php

namespace Tests\External\DpdIreland;

use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Client;
use App\Modules\DpdIreland\src\Shipment;
use Tests\TestCase;

class DpdTest extends TestCase
{
//    use RefreshDatabase;

    public function if_record_id_matches()
    {
        // DPDCommonPreAdviceAPI.docx.pdf

        // RecordID CHR M “001”
        // Custom identifier for the consignment’s
        // request – will be return in the response
    }
    /**
     * @test
     */
    public function if_authenticates()
    {
        $auth = Client::getCachedAuthorization();
        $this->assertEquals('OK', $auth['authorization_response']['Status']);
    }

    /**
     * @test
     */
    public function if_authorization_is_cached()
    {
        $auth1 = Client::getCachedAuthorization();
        $auth2 = Client::getCachedAuthorization();

        $this->assertTrue($auth2['from_cache']);
        $this->assertEquals($auth1['authorization_response']['AccessToken'], $auth2['authorization_response']['AccessToken']);
    }

    /**
     * @test
     */
    public function successfully_generate_preadvice()
    {
        // be very careful,
        // xml column order needs to be kept
        $shipment = new Shipment([
            'Consignment' => [
                'RecordID' => 1,
                'CustomerAccount' => '',
                'TotalParcels'=> 1,
                'Relabel'=> 0,
                'ServiceOption' => 5,
                'ServiceType' => 1,
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
//                    'References' => [
//                        'Reference' => [
//                            'ReferenceName' => 'SHIP.TOWN',
//                            'ReferenceValue' => '',
//                            'ParcelNumber' => 1,
//                        ]
//                    ]
            ],

        ]);

        $preAdvice = Dpd::getPreAdvice($shipment);

        $this->assertEquals('OK', $preAdvice->status());
    }
}
