<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    protected $fillable = [
        'inventory_id',
        'product_id',
        'warehouse_id',
        'quantity_delta',
        'quantity_before',
        'quantity_after',
        'description',
        'user_id',
    ];

    public function inventory()
    {
        $this->belongsTo(Inventory::class);
    }

    public function product()
    {
        $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
