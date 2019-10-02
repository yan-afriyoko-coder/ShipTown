<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        "sku"
    ];

    protected $casts = [
        'quantity_reserved_details' => 'array'
    ];

    public function reserve($quantity, $comment) {

        $this->increment('quantity_reserved', $quantity);

        $this->addReservedComment($quantity, $comment);

        $this->save();

    }

    /**
     * @param $quantity
     * @param $comment
     */
    public function addReservedComment($quantity, $comment): void
    {
        $quantity_reserved_details = $this->quantity_reserved_details;

        $quantity_reserved_details[] = [
            "quantity" => $quantity,
            "comment" => $comment
        ];

        $this->quantity_reserved_details = $quantity_reserved_details;
    }
}
