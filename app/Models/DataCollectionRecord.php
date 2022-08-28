<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataCollectionRecord extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'user_id'
    ];
}
