<?php

namespace App\Models;

use App\User;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property DateTime|null picked_at
 */
class Pick extends Model
{
    protected $fillable = [
        'product_id',
        'sku_ordered',
        'name_ordered',
        'quantity_required',
        'picker_user_id',
        'picked_at'
    ];

    public function pick(User $picker)
    {
        $this->update([
            'picker_user_id' => $picker->getKey(),
            'picked_at' => now()
        ]);
    }

    public function wasJustPicked()
    {
        return isset($this->picked_at) && $this->getOriginal('picked_at') === null;
    }
}
