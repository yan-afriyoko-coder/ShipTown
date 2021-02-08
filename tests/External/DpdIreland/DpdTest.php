<?php

namespace Tests\External\DpdIreland;

use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Client;
use App\Modules\DpdIreland\src\Consignment;
use Tests\TestCase;

class DpdTest extends TestCase
{
//    use RefreshDatabase;

    public function if_record_id_matches()
    {
        $this->markTestIncomplete('Compare if record_id matches RecordID return in PreAdvice');
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
        $shipment = new Consignment([
                'TotalParcels'=> 1,
            'Relabel'=> 0,
            'ServiceOption' => 5,
            'RecordID' => 1,
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
        ]);

        $preAdvice = Dpd::getPreAdvice($shipment);

        $this->assertEquals('OK', $preAdvice->status());
    }
}
