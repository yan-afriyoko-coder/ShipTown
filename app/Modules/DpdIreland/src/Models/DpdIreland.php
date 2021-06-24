<?php

namespace App\Modules\DpdIreland\src\Models;

use App\Traits\Encryptable;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Configuration
 *
 * @property int $id
 * @property bool $live
 * @property string $token
 * @property string $user
 * @property string $password
 * @property string $contact
 * @property string $contact_telephone
 * @property string $contact_email
 * @property string $business_name
 * @property string $address_line_1
 * @property string $address_line_2
 * @property string $address_line_3
 * @property string $address_line_4
 * @property string $country_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|DpdIreland newModelQuery()
 * @method static Builder|DpdIreland newQuery()
 * @method static Builder|DpdIreland query()
 * @method static Builder|DpdIreland first()
 * @method static Builder|DpdIreland firstOrFail()
 * @mixin Eloquent
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
     * DpdIreland collection address as array
     *
     * @return array
     */
    public function getCollectionAddress(): array
    {
        return [
            'Contact' => $this->attributes['contact'],
            'ContactTelephone' => $this->attributes['contact_telephone'],
            'ContactEmail' => $this->attributes['contact_email'],
            'BusinessName' => $this->attributes['business_name'],
            'AddressLine1' => $this->attributes['address_line_1'],
            'AddressLine2' => $this->attributes['address_line_2'],
            'AddressLine3' => $this->attributes['address_line_3'],
            'AddressLine4' => $this->attributes['address_line_4'],
            'CountryCode' => $this->attributes['country_code'],
        ];
    }
}
