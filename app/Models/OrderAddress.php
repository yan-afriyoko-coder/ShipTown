<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\OrderAddress
 *
 * @property int $id
 * @property string $company
 * @property string $gender
 * @property string $first_name
 * @property string $last_name
 * @property string $full_name
 * @property string $address1
 * @property string $address2
 * @property string $postcode
 * @property string $city
 * @property string $state_code
 * @property string $state_name
 * @property string $country_code
 * @property string $country_name
 * @property string $phone
 * @property string $fax
 * @property string $website
 * @property string $region
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Order|null $order
 * @method static Builder|OrderAddress newModelQuery()
 * @method static Builder|OrderAddress newQuery()
 * @method static Builder|OrderAddress query()
 * @method static Builder|OrderAddress whereAddress1($value)
 * @method static Builder|OrderAddress whereAddress2($value)
 * @method static Builder|OrderAddress whereCity($value)
 * @method static Builder|OrderAddress whereCompany($value)
 * @method static Builder|OrderAddress whereCountryCode($value)
 * @method static Builder|OrderAddress whereCountryName($value)
 * @method static Builder|OrderAddress whereCreatedAt($value)
 * @method static Builder|OrderAddress whereDeletedAt($value)
 * @method static Builder|OrderAddress whereFax($value)
 * @method static Builder|OrderAddress whereFirstName($value)
 * @method static Builder|OrderAddress whereGender($value)
 * @method static Builder|OrderAddress whereId($value)
 * @method static Builder|OrderAddress whereLastName($value)
 * @method static Builder|OrderAddress wherePhone($value)
 * @method static Builder|OrderAddress wherePostcode($value)
 * @method static Builder|OrderAddress whereRegion($value)
 * @method static Builder|OrderAddress whereStateCode($value)
 * @method static Builder|OrderAddress whereStateName($value)
 * @method static Builder|OrderAddress whereUpdatedAt($value)
 * @method static Builder|OrderAddress whereWebsite($value)
 * @mixin Eloquent
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

    /**
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name .' '. $this->last_name;
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }
}
