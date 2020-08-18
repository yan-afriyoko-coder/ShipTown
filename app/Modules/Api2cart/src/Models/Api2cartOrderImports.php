<?php

namespace App\Modules\Api2cart\src\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use function PHPUnit\Framework\isNull;

/**
 * @property array raw_import
 */
class Api2cartOrderImports extends Model
{
    protected $table = 'api2cart_order_imports';

    protected $fillable = [
        'when_processed',
        'order_number',
        'raw_import',
    ];

    protected $casts = [
        'raw_import' => 'array',
    ];

    // we use attributes to set default values
    // we wont use database default values
    // as this is then not populated
    // correctly to events
    protected $attributes = [
        'raw_import' => '{}',
    ];


    public function extractShippingAddressAttributes()
    {
        // array_filter will cleanup null values
        return array_filter([
            'company'       => $this->raw_import['shipping_address']['company'],
            'gender'        => $this->raw_import['shipping_address']['gender'],
            'first_name'    => $this->raw_import['shipping_address']['first_name'],
            'last_name'     => $this->raw_import['shipping_address']['last_name'],
            'address1'      => $this->raw_import['shipping_address']['address1'],
            'address2'      => $this->raw_import['shipping_address']['address2'],
            'postcode'      => $this->raw_import['shipping_address']['postcode'],
            'city'          => $this->raw_import['shipping_address']['city'],
            'state_code'    => $this->raw_import['shipping_address']['state']['code'],
            'state_name'    => $this->raw_import['shipping_address']['state']['name'],
            'country_code'  => $this->raw_import['shipping_address']['country']['code3'],
            'country_name'  => $this->raw_import['shipping_address']['country']['name'],
            'phone'         => $this->raw_import['shipping_address']['phone'],
            'fax'           => $this->raw_import['shipping_address']['fax'],
            'website'       => $this->raw_import['shipping_address']['website'],
            'region'        => $this->raw_import['shipping_address']['region'],
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
                'sku'               => null,
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
     * @param bool $chronological
     * @return Collection
     */
    public function extractStatusHistory(array $order, bool $chronological = true)
    {
        $statuses = Collection::make($order['status']['history']);

        if($chronological) {
            $statuses = $statuses->sort(function ($a, $b) {
                $a_time = Carbon::make($a['modified_time']['value']);
                $b_time = Carbon::make($b['modified_time']['value']);
                return $a_time > $b_time;
            });
        }

        return $statuses;
    }

}
