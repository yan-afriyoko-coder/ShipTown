<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed id
 */
class OrderAddress extends Model
{
    protected $fillable = [
        'company',
        'gender',
        'first_name',
        'last_name',
        'address1',
        'address2',
        'postcode',
        'city',
        'state_code',
        'state_name',
        'country_code',
        'country_name',
        'phone',
        'fax',
        'website',
        'region',
    ];
}
