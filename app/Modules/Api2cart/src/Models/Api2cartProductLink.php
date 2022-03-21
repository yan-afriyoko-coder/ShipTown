<?php

namespace App\Modules\Api2cart\src\Models;

use App\BaseModel;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Modules\Api2cart\src\Services\Api2cartService;
use Barryvdh\LaravelIdeHelper\Eloquent;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Class Api2cartProductLink.
 *
 * @property int $id
 * @property int $product_id
 * @property Product $product
 * @property string|null $api2cart_product_type
 * @property string|null $api2cart_product_id
 * @property Carbon $last_fetched_at
 * @property array $last_fetched_data
 * @property Api2cartConnection $api2cartConnection
 * @property string|null $api2cart_connection_id
 * @property float|null $api2cart_quantity
 * @property float|null $api2cart_price
 * @property float|null $api2cart_sale_price
 * @property Carbon|null $api2cart_sale_price_start_date
 * @property Carbon|null $api2cart_sale_price_end_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Api2cartProductLink newModelQuery()
 * @method static Builder|Api2cartProductLink newQuery()
 * @method static Builder|Api2cartProductLink query()
 * @method static Builder|Api2cartProductLink whereApi2cartConnectionId($value)
 * @method static Builder|Api2cartProductLink whereApi2cartPrice($value)
 * @method static Builder|Api2cartProductLink whereApi2cartProductId($value)
 * @method static Builder|Api2cartProductLink whereApi2cartProductType($value)
 * @method static Builder|Api2cartProductLink whereApi2cartQuantity($value)
 * @method static Builder|Api2cartProductLink whereApi2cartSalePrice($value)
 * @method static Builder|Api2cartProductLink whereApi2cartSalePriceEndDate($value)
 * @method static Builder|Api2cartProductLink whereApi2cartSalePriceStartDate($value)
 * @method static Builder|Api2cartProductLink whereCreatedAt($value)
 * @method static Builder|Api2cartProductLink whereId($value)
 * @method static Builder|Api2cartProductLink whereLastFetchedAt($value)
 * @method static Builder|Api2cartProductLink whereLastFetchedData($value)
 * @method static Builder|Api2cartProductLink whereProductId($value)
 * @method static Builder|Api2cartProductLink whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Api2cartProductLink extends BaseModel
{
    protected $table = 'modules_api2cart_product_links';

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
        'sale_price_end_date',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'last_fetched_data' => 'array',
    ];

    /**
     * @param array $options
     *
     * @return bool
     */
    public function save(array $options = []): bool
    {
        if ($this->last_fetched_data) {
            $product_data = json_decode((string)$this->last_fetched_data);

            $this->api2cart_product_id = $product_data->id;
            $this->api2cart_quantity = $this->getQuantity((array)$product_data, $this->api2cartConnection);
            $this->api2cart_price = $product_data->price;

            $this->api2cart_sale_price = $product_data->special_price->value;

            $created_at = $product_data->special_price->created_at;
            $sprice_create = empty($created_at) ? '2000-01-01 00:00:00' : $created_at->value;
            $sprice_create = Carbon::createFromTimeString($sprice_create)->format('Y-m-d H:i:s');
            $this->api2cart_sale_price_start_date = Carbon::createFromTimeString($sprice_create)->format('Y-m-d H:i:s');

            $expired_at = $product_data->special_price->expired_at;
            $sprice_expired_at = empty($expired_at) ? '2000-01-01 00:00:00' : $expired_at->value;
            $sprice_expired_at = Carbon::createFromTimeString($sprice_expired_at)->format('Y-m-d H:i:s');
            $this->api2cart_sale_price_end_date = Carbon::createFromTimeString($sprice_expired_at)
                ->format('Y-m-d H:i:s');
        }

        return parent::save($options);
    }

    /**
     * @throws GuzzleException
     */
    public function isInSync(): bool
    {
        $product_data = $this->getProductData();

        $store_id = Arr::has($product_data, 'store_id') ? $product_data['store_id'] : null;

        if ($this->api2cart_product_type === null) {
            $this->updateTypeAndId()->save();
        }

        switch ($this->api2cart_product_type) {
            case 'product':
                $product_now = Api2cartService::getSimpleProductInfo($this->api2cartConnection, $this->product->sku);
                break;
            case 'variant':
                $product_now = Api2cartService::getVariantInfo($this->api2cartConnection, $this->product->sku);
                break;
            default:
                Log::warning('Update Check FAILED - Could not find product', ['sku' => $product_data['sku']]);
                return false;
        }

        // if product data is null, product does not exist on eCommerce
        // we will delete link
        if (is_null($product_now)) {
            $this->forceDelete();
            return false;
        }

        $this->last_fetched_data = $product_now;

        $differences = $this->getDifferences($product_data, $product_now);

        if (empty($differences)) {
            return true;
        }

        Log::warning('Update Check FAILED', [
            'type' => $product_now['type'],
            'sku' => $product_now['sku'],
            'store_id' => $store_id,
            'differences' => $differences,
            'now' => $product_now
        ]);

        return false;
    }

    /**
     * @param array $expected
     * @param array $actual
     *
     * @return array
     */
    private function getDifferences(array $expected, array $actual): array
    {
        // initialize variables
        $differences = [];

        $keys_to_verify = [
            'price',
        ];

        if ((Arr::has($actual, 'manage_stock')) && ($actual['manage_stock'] != 'False')) {
            $keys_to_verify = array_merge($keys_to_verify, ['quantity']);
        }

        if ((Arr::has($expected, 'sprice_expire')) &&
            (\Carbon\Carbon::createFromTimeString($expected['sprice_expire'])->isFuture())) {
            $keys_to_verify = array_merge($keys_to_verify, [
                'special_price',
                'sprice_create',
                'sprice_expire',
            ]);
        }

        $expected_data = Arr::only($expected, $keys_to_verify);
        $actual_data = Arr::only($actual, $keys_to_verify);

        foreach (array_keys($expected_data) as $key) {
            if ((!Arr::has($actual_data, $key)) or ($expected_data[$key] != $actual_data[$key])) {
                $differences[$key] = [
                    'expected' => $expected_data[$key],
                    'actual' => $actual_data[$key],
                ];
            }
        }

        return Arr::dot($differences);
    }

    /**
     * @param array $product
     * @param Api2cartConnection|null $connection
     *
     * @return int
     */
    public static function getQuantity(array $product, Api2cartConnection $connection = null): int
    {
        if (is_null($connection) or is_null($connection->magento_warehouse_id)) {
            return $product['quantity'];
        }

        if (!key_exists($connection->magento_warehouse_id, $product['inventory'])) {
            return 0;
        }

        return $product['inventory'][$connection->magento_warehouse_id]['quantity'];
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
     */
    public function updateTypeAndId(): Api2cartProductLink
    {
        $store_key = $this->api2cartConnection->bridge_api_key;

        $response = Api2cartService::getProductTypeAndId($store_key, $this->product->sku);

        $this->api2cart_product_type = $response['type'];
        $this->api2cart_product_id = $response['id'];

        return $this;
    }

    /**
     * @return array
     */
    public function getProductData(): array
    {
        $data = collect();

        $data = $data->merge($this->getBasicData());
        $data = $data->merge($this->getMagentoStoreId());
        $data = $data->merge($this->getInventoryData());

        if (isset($this->api2cartConnection->pricing_location_id)) {
            $data = $data->merge($this->getPricingData());
        }

        return $data->toArray();
    }

    /**
     * @return array
     */
    private function getBasicData(): array
    {
        return [
            'product_id' => $this->product->getKey(),
            'sku' => $this->product->sku,
            'name' => $this->product->name,
            'description' => $this->product->name,
        ];
    }

    /**
     * @return array
     */
    private function getMagentoStoreId(): array
    {
        return [
            'store_id' => $this->api2cartConnection->magento_store_id ?? 0,
        ];
    }

    /**
     * @return array
     */
    private function getInventoryData(): array
    {
        // we will refresh to get the latest data
        $product = $this->product->refresh();

        $query = Inventory::whereProductId($product->getKey());

        if ($this->api2cartConnection->inventory_warehouse_ids) {
            $query->whereIn('warehouse_id', $this->api2cartConnection->inventory_warehouse_ids);
        }

        $quantity_available = floor($query->sum('quantity_available'));

        return [
            'quantity' => $quantity_available ?? 0,
            'in_stock' => $quantity_available > 0 ? 'True' : 'False',
        ];
    }

    /**
     * @return array
     */
    private function getPricingData(): array
    {
        $productPrice = ProductPrice::query()->where([
                'product_id' => $this->product->getKey(),
                'location_id' => $this->api2cartConnection->pricing_location_id,
            ])
            ->first();

        return [
            'price' => $productPrice->price,
            'special_price' => $productPrice->sale_price,
            'sprice_create' => Api2cartService::formatDateForApi2cart($productPrice->sale_price_start_date),
            'sprice_expire' => Api2cartService::formatDateForApi2cart($productPrice->sale_price_end_date),
        ];
    }
}
