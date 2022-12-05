<?php



namespace Database\Factories\Modules\DpdIreland\src\Models;

use Illuminate\Database\Eloquent\Factories\Factory;

class DpdIrelandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'live'              => false,
            'token'             => env('TEST_DPD_TOKEN', $this->faker->randomNumber(9)),
            'user'              => env('TEST_DPD_USER', $this->faker->randomNumber(6)),
            'password'          => env('TEST_DPD_PASSWORD', $this->faker->randomNumber(6)),
            'contact'           => 'John Smith',
            'contact_telephone' => '12345678901',
            'contact_email'     => 'john.smith@dpd.ie',
            'business_name'     => 'JS Business',
            'address_line_1'    => 'DPD Ireland, Westmeath',
            'address_line_2'    => 'Unit 2B Midland Gateway Bus',
            'address_line_3'    => 'Kilbeggan',
            'address_line_4'    => 'Westmeath',
            'country_code'      => 'IE',
        ];
    }
}
