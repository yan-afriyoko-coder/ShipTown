<?php

namespace App\Modules\Api2cart\src\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Modules\Api2cart\src\Models\Api2cartOrderImports.
 *
 * @property int                             $id
 * @property int|null                        $connection_id
 * @property int|null                        $order_id
 * @property string|null                     $when_processed
 * @property string|null                     $order_number
 * @property array                           $raw_import
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder|Api2cartOrderImports newModelQuery()
 * @method static Builder|Api2cartOrderImports newQuery()
 * @method static Builder|Api2cartOrderImports query()
 * @method static Builder|Api2cartOrderImports whereConnectionId($value)
 * @method static Builder|Api2cartOrderImports whereCreatedAt($value)
 * @method static Builder|Api2cartOrderImports whereId($value)
 * @method static Builder|Api2cartOrderImports whereOrderId($value)
 * @method static Builder|Api2cartOrderImports whereOrderNumber($value)
 * @method static Builder|Api2cartOrderImports whereRawImport($value)
 * @method static Builder|Api2cartOrderImports whereUpdatedAt($value)
 * @method static Builder|Api2cartOrderImports whereWhenProcessed($value)
 * @mixin Eloquent
 */
class Api2cartOrderImports extends Model
{
    /**
     * @var string
     */
    protected $table = 'api2cart_order_imports';

    /**
     * @var string[]
     */
    protected $fillable = [
        'when_processed',
        'order_number',
        'raw_import',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'raw_import' => 'array',
    ];

    // we use attributes to set default values
    // we wont use database default values
    // as this is then not populated
    // correctly to events
    /**
     * @var string[]
     */
    protected $attributes = [
        'raw_import' => '{}',
    ];

    /**
     * @return array
     */
    public function extractShippingAddressAttributes(): array
    {
        $shipping_address = $this->raw_import['shipping_address'];

        // array_filter will cleanup null values
        return array_filter([
            'company'       => $shipping_address['company'],
            'gender'        => $shipping_address['gender'],
            'first_name'    => $shipping_address['first_name'],
            'last_name'     => $shipping_address['last_name'],
            'address1'      => $shipping_address['address1'],
            'address2'      => $shipping_address['address2'],
            'postcode'      => $shipping_address['postcode'],
            'city'          => $shipping_address['city'],
            'state_code'    => $shipping_address['state'] ? $shipping_address['state']['code'] : '',
            'state_name'    => $shipping_address['state'] ? $shipping_address['state']['name'] : '',
            'country_code'  => $shipping_address['country']['code3'],
            'country_name'  => $shipping_address['country']['name'],
            'phone'         => $shipping_address['phone'],
            'fax'           => $shipping_address['fax'],
            'website'       => $shipping_address['website'],
            'region'        => $shipping_address['region'],
        ]);
    }

    /**
     * @return array
     */
    public function extractOrderProducts(): array
    {
        $result = [];

        foreach ($this->raw_import['order_products'] as $rawOrderProduct) {
            $result[] = [
                //                'sku'               => null,
                'sku_ordered'       => $rawOrderProduct['model'],
                'name_ordered'      => $rawOrderProduct['name'],
                'quantity_ordered'  => $rawOrderProduct['quantity'],
                'price'             => $rawOrderProduct['price'],
            ];
        }

        return $result;
    }

    /**
     * @param array $order
     * @param bool  $chronological
     *
     * @return Collection
     */
    public function extractStatusHistory(array $order = null, bool $chronological = true)
    {
        $statuses = Collection::make($this['raw_import']['status']['history']);

        if ($chronological) {
            $statuses = $statuses->sort(function ($a, $b) {
                $a_time = Carbon::make($a['modified_time']['value']);
                $b_time = Carbon::make($b['modified_time']['value']);

                return $a_time > $b_time;
            });
        }

        return $statuses;
    }

    /**
     * @return Carbon
     */
    public function ordersCreateAt(): Carbon
    {
        return Carbon::createFromTimeString($this->raw_import['create_at']['value']);
    }
}
