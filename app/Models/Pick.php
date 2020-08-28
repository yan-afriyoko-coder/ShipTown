<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Pick extends Model
{
    protected $fillable = [
        'product_id',
        'sku_ordered',
        'name_ordered',
        'quantity_required'
    ];

    public function pick(User $picker)
    {
        $this->update([
            'picker_user_id' => $picker->getKey(),
            'picked_at' => now()
        ]);
    }
}
