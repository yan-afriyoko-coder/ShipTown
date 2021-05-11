<?php

namespace App\Modules\Api2cart\src\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Api2cartProductLink
 * @package App\Modules\Api2cart\src\Models
 *
 * @property int $id
 * @property int $product_id
 * @property string|null $api2cart_product_id
 * @property Carbon $last_fetched_at
 * @property array $last_fetched_data
 * @property double api2cart_quantity
 * @property double api2cart_price
 * @property double api2cart_sale_price
 * @property Carbon api2cart_sale_price_start_date
 * @property Carbon api2cart_sale_price_end_date
 * @property Api2cartConnection $api2cartConnection
 *
 */
class Api2cartProductLink extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'product_id',
        'api2cart_connection_id',
        'api2cart_product_type',
        'api2cart_product_id',
        'last_fetched_at',
        'last_fetched_data',
        'api2cart_quantity',
        'api2cart_price',
        'api2cart_sale_price',
        'api2cart_sale_price_start_date',
        'api2cart_sale_price_end_date',
    ];

    protected $dates = [
        'sale_price_start_date',
        'sale_price_end_date'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'last_fetched_data' => 'array'
    ];

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = []): bool
    {
        if ($this->last_fetched_data) {
            $product_data = json_decode((string)$this->last_fetched_data);

            $this->api2cart_product_id = $product_data->id;
            $this->api2cart_quantity = $this->getQuantity($product_data, $this->api2cartConnection);
            $this->api2cart_price = $product_data->price;
            $this->api2cart_sale_price = $product_data->special_price->value;

            $specialPrice = $product_data->special_price;
            $this->api2cart_sale_price_start_date = Carbon::createFromTimeString($specialPrice->created_at)
                ->format("Y-m-d H:i:s");
            $this->api2cart_sale_price_end_date = Carbon::createFromTimeString($specialPrice->expired_at)
                ->format("Y-m-d H:i:s");
        }

        return parent::save($options);
    }


    /**
     * @param $product
     * @param Api2cartConnection $connection
     * @return int
     */
    private function getQuantity($product, Api2cartConnection $connection): int
    {
        if (is_null($connection->magento_warehouse_id)) {
            return $product->quantity;
        }

        return $product->inventory[0]->quantity;
    }

    /**
     * @return BelongsTo
     */
    public function api2cartConnection(): BelongsTo
    {
        return $this->belongsTo(Api2cartConnection::class, 'api2cart_connection_id');
    }
}
