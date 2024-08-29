<?php

namespace Tests\Feature\Api\ShippingAddress;

use App\User;
use Tests\TestCase;
use App\Models\OrderAddress;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_updates_shipping_address_successfully()
    {
        $this->withoutMockingConsoleOutput(); // Tambahkan ini

        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $orderAddress = OrderAddress::factory()->create(); 

        $data = [
            'id' => $orderAddress->id,
            'city' => 'Updated City',
            'country' => 'Updated Country',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'company' => 'Test Company',
            'address1' => '123 Main St',
            'address2' => 'Suite 100',
            'postcode' => '12345',
            'country_code' => 'US',
            'country_name' => 'United States',
            'fax' => '0987654321',
            'region' => 'Region',
            'state_code' => 'ST',
            'state_name' => 'State',
            'website' => 'http://example.com',
        ];

        $response = $this->json('POST', route('api.shipping-address.store'), $data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'address1' => '123 Main St'
        ]);
    }

    public function test_store_returns_error_when_address_not_found()
    {
        $this->withoutMockingConsoleOutput(); // Tambahkan ini

        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $data = [
            'id' => 999, // Pastikan ID ini tidak ada
            'city' => 'Updated City',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'company' => 'Test Company',
            'address1' => '123 Main St',
            'address2' => 'Suite 100',
            'postcode' => '12345',
            'country_code' => 'US',
            'country_name' => 'United States',
            'fax' => '0987654321',
            'region' => 'Region',
            'state_code' => 'ST',
            'state_name' => 'State',
            'website' => 'http://example.com',
        ];

        $response = $this->json('POST', route('api.shipping-address.store'), $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(['error' => 'Shipping Address not found.']);
    }

}
