<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $role = $this->roles()->first();

        return [
            'id'                      => $this->getKey(),
            'name'                    => $this->name,
            'email'                   => $this->email,
            'warehouse_id'            => $this->warehouse_id,
            'location_id'             => $this->location_id,
            'role_id'                 => $role ? $role->getKey() : null,
            'role_name'               => $role ? $role->name : null,
            'printer_id'              => $this->printer_id,
            'address_label_template'  => $this->address_label_template,
            'ask_for_shipping_number' => $this->ask_for_shipping_number,
        ];
    }
}
