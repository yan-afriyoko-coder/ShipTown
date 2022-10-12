<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        $role = $this->roles()->first();

        return [
            'id'                      => $this->getKey(),
            'name'                    => $this->name,
            'email'                   => $this->email,
            'warehouse_id'            => $this->warehouse_id,
            'default_dashboard_uri'   => $this->default_dashboard_uri,
            'location_id'             => $this->location_id,
            'role_id'                 => $role ? $role->getKey() : null,
            'role_name'               => $role ? $role->name : null,
            'printer_id'              => $this->printer_id,
            'address_label_template'  => $this->address_label_template,
            'ask_for_shipping_number' => $this->ask_for_shipping_number,

            'warehouse'               => WarehouseResource::make($this->whenLoaded('warehouse'))
        ];
    }
}
