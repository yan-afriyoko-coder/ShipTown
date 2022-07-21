<?php

namespace App\Modules\Api2cart\src\Models;

use App\BaseModel;
use App\Models\Inventory;
use App\Models\Product;
use App\Modules\Api2cart\src\Services\Api2cartService;
use App\Modules\Api2cart\src\Transformers\ProductTransformer;
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
 * @property array raw_import
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
        'last_fetched_at',
        'api2cart_connection_id',
        'api2cart_product_type',
        'api2cart_product_id',
        'api2cart_quantity',
        'api2cart_price',
        'api2cart_sale_price',
        'api2cart_sale_price_start_date',
        'api2cart_sale_price_end_date',
        'last_fetched_data',
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

    public function setLastFetchedDataAttribute($value)
    {
        $this->attributes['last_fetched_data'] = json_encode($value);

        $this->last_fetched_at                = now();

        $sprice_create = data_get($value, 'sprice_create', '2000-01-01 00:00:00');
        $sprice_expire = data_get($value, 'sprice_expire', '2000-01-01 00:00:00');

        $this->api2cart_product_id            = data_get($value, 'id');
        $this->api2cart_product_type          = data_get($value, 'type');
        $this->api2cart_quantity              = data_get($value, 'quantity');
        $this->api2cart_price                 = data_get($value, 'price');
        $this->api2cart_sale_price            = data_get($value, 'special_price');
        $this->api2cart_sale_price_start_date = Carbon::createFromTimeString($sprice_create)->format('Y-m-d H:i:s');
        $this->api2cart_sale_price_end_date   = Carbon::createFromTimeString($sprice_expire)->format('Y-m-d H:i:s');
    }
    /**
     */
    public function isInSync(): bool
    {
        if ($this->api2cart_product_type === 'configurable') {
            return true;
        }

        $api2cartDataExpected = ProductTransformer::toApi2cartPayload($this);

        $api2cartDataActual = [
            'type'          => $this->api2cart_product_type,
            'id'            => $this->api2cart_product_id,
            'quantity'      => $this->api2cart_quantity,
            'price'         => $this->api2cart_price,
            'special_price' => $this->api2cart_sale_price,
            'sprice_create' => Api2cartService::formatDateForApi2cart($this->api2cart_sale_price_start_date),
            'sprice_expire' => Api2cartService::formatDateForApi2cart($this->api2cart_sale_price_end_date),
        ];

        $differences = $this->getDifferences($api2cartDataExpected, $api2cartDataActual);

        if (empty($differences)) {
            return true;
        }

        Log::warning('Update Check FAILED', [
            'type' => $this->api2cart_product_type,
            'sku' => $this->product->sku,
            'differences' => $differences,
            'now' => $api2cartDataActual
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

        $keys_to_verify = [];

        if (data_get($actual, 'manage_stock', 'False') != 'False') {
            $keys_to_verify = array_merge($keys_to_verify, ['quantity']);
        }

        if (data_get($expected, 'quantity', 0) > 0) {
            $keys_to_verify = array_merge($keys_to_verify, ['price']);

            if ((Arr::has($expected, 'sprice_expire'))
                && (\Carbon\Carbon::createFromTimeString($expected['sprice_expire'])->isFuture())) {
                $keys_to_verify = array_merge($keys_to_verify, [
                    'special_price',
                    'sprice_create',
                    'sprice_expire',
                ]);
            }
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
     * @throws GuzzleException
     */
    public function fetchFromApi2cart(): void
    {
        if ($this->api2cart_product_type === null) {
            $this->updateTypeAndId()->save();
        }

        switch ($this->api2cart_product_type) {
            case 'simple':
            case 'product':
                $product_now = Api2cartService::getSimpleProductInfoByID(
                    $this->api2cartConnection,
                    $this->api2cart_product_id
                );
                break;
            case 'variant':
                $product_now = Api2cartService::getVariantInfoByID(
                    $this->api2cartConnection,
                    $this->api2cart_product_id
                );
                break;
            case 'configurable':
                $product_now = null;
                break;
            default:
                $product_now = null;
                Log::warning('Update Check FAILED - Could not find product', ['sku' => $this->product->sku]);
                break;
        }

        $this->last_fetched_data = $product_now;
        $this->save();
    }
}
