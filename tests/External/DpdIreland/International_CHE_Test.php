<?php

namespace Tests\External\DpdIreland;

use App\Models\Order;
use App\Models\OrderAddress;
use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Consignment;
use Exception;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class International_CHE_Test extends TestCase
{
    use SeedDpdTestConfiguration;

    public function test_if_this_is_completed()
    {
        $this->markTestIncomplete();
    }

    public function test_normal_overnight_consignment_single_parcel()
    {
        $address = factory(OrderAddress::class)->create([
            'company'      => 'DPD Test',
            'first_name'   => 'DPD',
            'last_name'    => 'Depot',
            'address1'     => 'Maisbühlstrasse 50',
            'address2'     => '',
            'phone'        => '0861230000',
            'city'         => 'Unterägeri',
            'state_name'   => 'ZG',
            'postcode'     => '6314',
            'country_code' => 'CHE',
        ]);

        $order = factory(Order::class)->create([
            'shipping_address_id' => $address->getKey(),
        ]);

        try {
            Dpd::shipOrder($order);
            $success = true;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->fail($e->getMessage());
        }

        // we just want no exceptions
        $this->assertTrue($success);
    }
//
//    /**
//     * @test
//     */
//    public function test_if_successfully_generates_preadvice()
//    {
//        $consignment = new Consignment([
//            'RecordID'        => 1,
//            'TotalParcels'    => 1,
//            'ServiceOption'   => 5,
//            'ServiceType'     => 1,
//            'DeliveryAddress' => [
//                'Contact'          => 'John Smith',
//                'ContactTelephone' => '12345678901',
//                'ContactEmail'     => 'john.smith@ie.ie',
//                'BusinessName'     => 'JS Business',
//                'AddressLine1'     => 'Maisbühlstrasse 50',
//                'AddressLine2'     => '',
//                'AddressLine3'     => 'Unterägeri',
//                'AddressLine4'     => 'ZG',
//                'CountryCode'      => 'CHE',
//            ],
//            'CollectionAddress' => [
//                'Contact'          => 'John Smith',
//                'ContactTelephone' => '12345678901',
//                'ContactEmail'     => 'john.smith@ie.ie',
//                'BusinessName'     => 'JS Business',
//                'AddressLine1'     => 'DPD Ireland, Westmeath',
//                'AddressLine2'     => 'Unit 2B Midland Gateway Bus',
//                'AddressLine3'     => 'Kilbeggan',
//                'AddressLine4'     => 'Westmeath',
//                'CountryCode'      => 'IE',
//            ],
//        ]);
//
//        $preAdvice = Dpd::getPreAdvice($consignment);
//
//        $this->assertTrue($preAdvice->isSuccess());
//    }
}
