<?php

namespace App\Modules\DpdIreland\src\Models;

use App\Traits\Encryptable;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Configuration.
 *
 * @property int         $id
 * @property bool        $live
 * @property string      $token
 * @property string      $user
 * @property string      $password
 * @property string      $contact
 * @property string      $contact_telephone
 * @property string      $contact_email
 * @property string      $business_name
 * @property string      $address_line_1
 * @property string      $address_line_2
 * @property string      $address_line_3
 * @property string      $address_line_4
 * @property string      $country_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|DpdIreland newModelQuery()
 * @method static Builder|DpdIreland newQuery()
 * @method static Builder|DpdIreland query()
 * @method static Builder|DpdIreland first()
 * @method static Builder|DpdIreland firstOrFail()
 * @mixin Eloquent
 *
 * @method static Builder|DpdIreland whereAddressLine1($value)
 * @method static Builder|DpdIreland whereAddressLine2($value)
 * @method static Builder|DpdIreland whereAddressLine3($value)
 * @method static Builder|DpdIreland whereAddressLine4($value)
 * @method static Builder|DpdIreland whereBusinessName($value)
 * @method static Builder|DpdIreland whereContact($value)
 * @method static Builder|DpdIreland whereContactEmail($value)
 * @method static Builder|DpdIreland whereContactTelephone($value)
 * @method static Builder|DpdIreland whereCountryCode($value)
 * @method static Builder|DpdIreland whereCreatedAt($value)
 * @method static Builder|DpdIreland whereId($value)
 * @method static Builder|DpdIreland whereLive($value)
 * @method static Builder|DpdIreland wherePassword($value)
 * @method static Builder|DpdIreland whereToken($value)
 * @method static Builder|DpdIreland whereUpdatedAt($value)
 * @method static Builder|DpdIreland whereUser($value)
 */
class DpdIreland extends Model
{
    use Encryptable;

    protected $table = 'modules_dpd-ireland_configuration';

    protected $fillable = [
        'live',
        'token',
        'user',
        'password',
        'contact',
        'contact_telephone',
        'contact_email',
        'business_name',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'address_line_4',
        'country_code',
    ];

    protected $encryptable = [
        'token', 'user', 'password',
    ];

    /**
     * DpdIreland collection address as array.
     *
     * @return array
     */
    public function getCollectionAddress(): array
    {
        return [
            'Contact'          => $this->attributes['contact'],
            'ContactTelephone' => $this->attributes['contact_telephone'],
            'ContactEmail'     => $this->attributes['contact_email'],
            'BusinessName'     => $this->attributes['business_name'],
            'AddressLine1'     => $this->attributes['address_line_1'],
            'AddressLine2'     => $this->attributes['address_line_2'],
            'AddressLine3'     => $this->attributes['address_line_3'],
            'AddressLine4'     => $this->attributes['address_line_4'],
            'CountryCode'      => $this->attributes['country_code'],
        ];
    }
}
