<?php

namespace Tests\External\ScurriAnpost;

use App\Modules\ScurriAnpost\src\Api\Client;
use Exception;
use Tests\TestCase;

class ClientTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @throws Exception
     */
    public function test_successful_get_carriers()
    {
        $response = Client::getCarriers();

        $this->assertEquals(200, $response->status());
    }

    /**
     * @throws Exception
     */
    public function test_successful_create_consignment()
    {
        $consignment = Client::createConsignment([
            "order_number" => "8384832",
            "recipient" => [
                "address" => [
                    "country" => "IE",
                    "postcode" => "Y35 DW6E",
                    "city" => "Wexford",
                    "address2" => "The Bullring",
                    "address1" => "Innovation House",
                    "state" => "Wexford"
                  ],
                "contact_number" => "072 8848292234",
                "email_address" => "john@scurri.com",
                "company_name" => "Scurri",
                "name" => "John Doe"
            ],
            "packages" => [
                [
                    "items" => [
                        [
                            "sku" => "n/a",
                            "quantity" => 1,
                            "name" => "Shipment"
                        ]
                    ],
                    "length" => 10,
                    "height" => 0.5,
                    "width" => 12.2,
                    "reference" => "CustomCustomerReference1"
                ],
            ],
        ]);

        $this->assertEquals(1, $consignment->success->count());
    }

    /**
     * @throws Exception
     */
    public function test_successful_getPdfLabel()
    {
        $consignment = Client::createConsignment([
            "order_number" => "8384832",
            "recipient" => [
                "address" => [
                    "country" => "IE",
                    "postcode" => "Y35 DW6E",
                    "city" => "Wexford",
                    "address2" => "The Bullring",
                    "address1" => "Innovation House",
                    "state" => "Wexford"
                  ],
                "contact_number" => "072 8848292234",
                "email_address" => "john@scurri.com",
                "company_name" => "Scurri",
                "name" => "John Doe"
            ],
            "packages" => [
                [
                    "items" => [
                        [
                            "sku" => "n/a",
                            "quantity" => 1,
                            "name" => "Shipment"
                        ]
                    ],
                    "length" => 10,
                    "height" => 0.5,
                    "width" => 12.2,
                    "reference" => "CustomCustomerReference1"
                ],
            ],
        ]);

        $documents = Client::getDocuments($consignment);

        $this->assertNotNull($documents->getLabels());
    }
}
