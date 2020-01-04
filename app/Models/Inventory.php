<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    //

    protected $appends = [
        "quantity_available"
    ];

    public function getQuantityAvailableAttribute()
    {
        $quantity_available = $this->quantity - $this->quantity_reserved;

        if ($quantity_available<0) {
            return 0;
        }

        return $quantity_available;
    }

}
