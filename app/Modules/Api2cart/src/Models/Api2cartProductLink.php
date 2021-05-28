<?php

namespace App\Modules\Api2cart\src\Models;

use App\BaseModel;
use App\Models\Product;
use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Products;
use App\Modules\Api2cart\src\RequestResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Api2cartProductLink
 * @package App\Modules\Api2cart\src\Models
 *
 * @property int $id
 * @property int $product_id
 * @property Product $product
 * @property string|null $api2cart_product_type
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
class Api2cartProductLink extends BaseModel
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
            $this->api2cart_quantity = $this->getQuantity((array) $product_data, $this->api2cartConnection);
            $this->api2cart_price = $product_data->price;

            $this->api2cart_sale_price = $product_data->special_price->value;

            $created_at = $product_data->special_price->created_at;
            $sprice_create = empty($created_at) ? "2000-01-01 00:00:00" : $created_at->value;
            $sprice_create = Carbon::createFromTimeString($sprice_create)->format("Y-m-d H:i:s");
            $this->api2cart_sale_price_start_date = Carbon::createFromTimeString($sprice_create)->format("Y-m-d H:i:s");

            $expired_at = $product_data->special_price->expired_at;
            $sprice_expired_at = empty($expired_at) ? "2000-01-01 00:00:00" : $expired_at->value;
            $sprice_expired_at = Carbon::createFromTimeString($sprice_expired_at)->format("Y-m-d H:i:s");
            $this->api2cart_sale_price_end_date = Carbon::createFromTimeString($sprice_expired_at)->format("Y-m-d H:i:s");
        }

        return parent::save($options);
    }


    /**
     * @param array $product
     * @param Api2cartConnection|null $connection
     * @return int
     */
    public static function getQuantity(array $product, Api2cartConnection $connection = null): int
    {
        if (is_null($connection) or is_null($connection->magento_warehouse_id)) {
            return $product['quantity'];
        }

        if (! key_exists($connection->magento_warehouse_id, $product['inventory'])) {
            return 0;
        }

        return $product["inventory"][$connection->magento_warehouse_id]['quantity'];
    }

    /**
     * @return BelongsTo
     */
    public function api2cartConnection(): BelongsTo
    {
        return $this->belongsTo(Api2cartConnection::class, 'api2cart_connection_id');
    }


    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @throws RequestException
     */
    public function updateTypeAndId(): Api2cartProductLink
    {
        $response = Products::getProductTypeAndId($this->api2cartConnection->bridge_api_key, $this->product->sku);

        $this->api2cart_product_type = $response['type'];
        $this->api2cart_product_id = $response['id'];

        return $this;
    }

    /**
     * @throws RequestException
     */
    public function updateOrCreate($product_data): RequestResponse
    {
        $store_key = $this->api2cartConnection->bridge_api_key;

        if ($this->api2cart_product_type === null) {
            $this->updateTypeAndId()->save();
        }

        switch ($this->api2cart_product_type) {
            case "product":
                $properties = array_merge($product_data, ['id' => $this->api2cart_product_id]);
                return Products::updateSimpleProduct($store_key, $properties);
            case "variant":
                $properties = array_merge($product_data, ['id' => $this->api2cart_product_id]);
                return Products::updateVariant($store_key, $properties);
            default:
                return Products::createSimpleProduct($store_key, $product_data);
        }
    }
}
