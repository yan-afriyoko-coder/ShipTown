<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DpdIrelandConfigurationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'api_username' => $this->user,
            'api_password' => $this->password,
            'api_token' => $this->token,
            'collection_contact' => $this->contact,
            'collection_telephone' => $this->telephone,
            'collection_email' => $this->email,
            'collection_business_name' => $this->business_name,
            'collection_address_line_1' => $this->address_line_1,
            'collection_address_line_2' => $this->address_line_2,
            'collection_address_line_3' => $this->address_line_3,
            'collection_address_line_4' => $this->address_line_4,
            'collection_country_code' => $this->country_code,
        ];
    }
}
