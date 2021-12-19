<?php

namespace App\Modules\Api2cart\src\Services;

use App\Modules\Api2cart\src\Api\Client;
use App\Modules\Api2cart\src\Api\Products;
use App\Modules\Api2cart\src\Api\RequestResponse;
use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class Api2cartService
{
    private const PRODUCT_ALLOWED_KEYS = [
        'id',
        'sku',
        'model',
        'name',
        'description',
        'price',
        'special_price',
        'sprice_create',
        'sprice_expire',
        'quantity',
        'in_stock',
        'store_id',
        'warehouse_id',
    ];

    private const PRODUCT_DONT_UPDATE_KEYS = [
        'model',
        'sku',
        'name',
        'description',
    ];

    /**
     * @param string $store_key
     * @param string $sku
     *
     * @return int|null
     */
    public static function getVariantID(string $store_key, string $sku): ?int
    {
        $cache_key = $store_key.'_'.$sku.'_variant_id';

        $id = Cache::get($cache_key);

        if ($id) {
            return $id;
        }

        try {
            $response = Products::productChildItemFind($store_key, $sku);
        } catch (Exception $exception) {
            return null;
        }

        if (isset($response) && ($response->isNotSuccess())) {
            return null;
        }

        $id = $response->getResult()['children'][0]['id'];

        Cache::put($cache_key, $id, 60 * 24 * 7);

        return $id;
    }

    /**
     * @param string $store_key
     * @param array  $product_data
     *
     * @return RequestResponse
     *
     */
    public static function createSimpleProduct(string $store_key, array $product_data): RequestResponse
    {
//        dd($product_data);
        $fields = [
            'sku',
            'model',
            'name',
            'description',
            'price',
            'quantity',
        ];

        $product = Arr::only($product_data, $fields);

        if (!Arr::has($product_data, 'model')) {
            $product['model'] = $product_data['sku'];
        }

        // disable new products
        $product['available_for_view'] = false;
        $product['available_for_sale'] = false;

        return Products::add($store_key, $product);
    }

    /**
     * @param string $store_key
     * @param array $data
     * @param bool $recursive
     * @return RequestResponse|null
     * @throws GuzzleException
     * @throws RequestException
     */
    public static function updateSimpleProduct(string $store_key, array $data, bool $recursive = true): ?RequestResponse
    {
        $product = collect($data)
            ->only(self::PRODUCT_ALLOWED_KEYS)
            ->except(self::PRODUCT_DONT_UPDATE_KEYS)
            ->merge([
                'reindex'     => 'False',
                'clear_cache' => 'False',
            ])
            ->toArray();

        try {
            return Client::GET($store_key, 'product.update.json', $product);
        } catch (RequestException $exception) {
            Api2cartProductLink::where(['api2cart_product_id' => $data['id']])->forceDelete();

            if ($exception->getCode() === RequestResponse::RETURN_CODE_MODEL_NOT_FOUND) {
                Products::assignStore($store_key, $data['id'], $data['store_id']);

                if ($recursive) {
                    return self::updateSimpleProduct($store_key, $data, false);
                }

                return null;
            }

            throw $exception;
        }
    }

    /**
     * This will only update variant product, will not update simple product.
     *
     * @param string $store_key
     * @param array $data
     * @param bool $recursive
     * @return RequestResponse|null
     * @throws GuzzleException
     * @throws RequestException
     */
    public static function updateVariant(string $store_key, array $data, bool $recursive = true): ?RequestResponse
    {
        $properties = collect($data)
            ->only(self::PRODUCT_ALLOWED_KEYS)
            ->except(self::PRODUCT_DONT_UPDATE_KEYS)
            ->merge([
                'reindex'     => 'False',
                'clear_cache' => 'False',
            ])
            ->toArray();

        try {
            return Client::GET($store_key, 'product.variant.update.json', $properties);
        } catch (RequestException $exception) {
            if ($exception->getCode() === RequestResponse::RETURN_CODE_STORE_ID_NOT_SUPPORTED) {
                Products::assignStore($store_key, $properties['id'], $properties['store_id']);

                if ($recursive) {
                    return self::updateVariant($store_key, $properties, false);
                }

                return null;
            } elseif ($exception->getCode() === RequestResponse::RETURN_CODE_MODEL_NOT_FOUND) {
                Products::assignStore($store_key, $properties['id'], $properties['store_id']);

                if ($recursive) {
                    return self::updateVariant($store_key, $properties, false);
                }

                return null;
            }

            throw $exception;
        }
    }

    /**
     * @param string $store_key
     * @param array  $product_data
     *
     * @return RequestResponse|null
     * @throws Exception|GuzzleException
     *
     */
    public static function updateProduct(string $store_key, array $product_data): ?RequestResponse
    {
        $product = self::getProductTypeAndId($store_key, $product_data['sku']);

        switch ($product['type']) {
            case 'product':
                $properties = array_merge($product_data, ['id' => $product['id']]);
                return self::updateSimpleProduct($store_key, $properties);
            case 'variant':
                $properties = array_merge($product_data, ['id' => $product['id']]);
                return self::updateVariant($store_key, $properties);
            default:
                logger('Cannot update - SKU not found', [$product_data['sku']]);
                break;
        }

        return null;
    }


    /**
     * @param Api2cartConnection $connection
     * @param array              $product_data
     *
     * @return RequestResponse
     * @throws Exception|GuzzleException
     *
     * @throws RequestException
     */
    public static function productUpdateOrCreate(Api2cartConnection $connection, array $product_data): RequestResponse
    {
        $product = self::getProductTypeAndId($connection->bridge_api_key, $product_data['sku']);

        switch ($product['type']) {
            case 'product':
                $properties = array_merge($product_data, ['id' => $product['id']]);
                return self::updateSimpleProduct($connection->bridge_api_key, $properties);
            case 'variant':
                $properties = array_merge($product_data, ['id' => $product['id']    ]);
                return self::updateVariant($connection->bridge_api_key, $properties);
            default:
                self::createSimpleProduct($connection->bridge_api_key, $product_data)->isSuccess();

                return self::updateSimpleProduct($connection->bridge_api_key, $product_data);
        }
    }

    /**
     * @param Api2cartConnection $conn
     * @param string $sku
     * @param array|null $fields
     *
     * @return array|null
     */
    public static function getSimpleProductInfo(Api2cartConnection $conn, string $sku, array $fields = null): ?array
    {
        $product_id = self::getSimpleProductID($conn->bridge_api_key, $sku);

        if (empty($product_id)) {
            return null;
        }

        $params = [
            'id'     => $product_id,
            'params' => implode(
                ',',
                $fields ?? [
                    'id',
                    'model',
                    'u_model',
                    'sku',
                    'u_sku',
                    'price',
                    'special_price',
                    'stores_ids',
                    'manage_stock',
                    'quantity',
                    'inventory',
                ]
            ),
        ];

        if ($conn->magento_store_id) {
            $params['store_id'] = $conn->magento_store_id;
        }

        $response = Client::GET($conn->bridge_api_key, 'product.info.json', $params);

        if ($response->isNotSuccess()) {
            return null;
        }

        $product = $response->getResult();

        $product['type'] = 'product';

        return self::transformProduct($product, $conn);
    }

    /**
     * @param Api2cartConnection $connection
     * @param string $sku
     * @param array|null $fields
     *
     * @return array|null
     *
     */
    public static function getVariantInfo(Api2cartConnection $connection, string $sku, array $fields = null): ?array
    {
        $variant_id = self::getVariantID($connection->bridge_api_key, $sku);

        if (empty($variant_id)) {
            return null;
        }

        $params = [
            'id'     => $variant_id,
            'params' => implode(
                ',',
                $fields ?? [
                    'id',
                    'model',
                    'u_model',
                    'sku',
                    'u_sku',
                    'price',
                    'special_price',
                    'stores_ids',
                    'quantity',
                    'inventory',
                ]
            ),
        ];

        if ($connection->magento_store_id) {
            $params['store_id'] = $connection->magento_store_id;
        }

        $response = Products::variantInfo($connection->bridge_api_key, $params);

        if ($response->isNotSuccess()) {
            return null;
        }

        $variant = $response->getResult()['variant'];

        $variant['type'] = 'variant';

        return self::transformProduct($variant, $connection);
    }

    /**
     * @param Api2cartConnection $connection
     * @param string $sku
     * @param array|null $fields
     *
     * @return array|null
     *
     */
    public static function getProductInfo(Api2cartConnection $connection, string $sku, array $fields = null): ?array
    {
        return self::getSimpleProductInfo($connection, $sku, $fields)
            ?? self::getVariantInfo($connection, $sku, $fields);
    }

    /**
     * @param string $store_key
     * @param string $sku
     *
     * @return int|null
     *
     */
    public static function getSimpleProductID(string $store_key, string $sku): ?int
    {
        $cache_key = $store_key.'_'.$sku.'_product_id';

        $id = Cache::get($cache_key);

        if ($id) {
            return $id;
        }

        $response = Products::find($store_key, [
            'find_where' => 'model',
            'find_value' => $sku,
            'store_id'   => 0,
        ]);

        if (isset($response) && ($response->isNotSuccess())) {
            return null;
        }

        $id = $response->getResult()['product'][0]['id'];

        Cache::put($cache_key, $id, 60 * 24 * 7);

        return $id;
    }

    /**
     * @param string $store_key
     * @param string $sku
     *
     * @return array
     *
     */
    public static function getProductTypeAndId(string $store_key, string $sku): array
    {
        $cached_product = Cache::get(self::getSkuCacheKey($store_key, $sku));

        if ($cached_product) {
            return $cached_product;
        }

        $product_id = self::getSimpleProductID($store_key, $sku);

        if ($product_id) {
            $product = [
                'type' => 'product',
                'id'   => $product_id,
            ];

            Cache::put(self::getSkuCacheKey($store_key, $sku), $product, 60 * 24 * 7);

            return $product;
        }

        $variant_id = self::getVariantID($store_key, $sku);

        if (!empty($variant_id)) {
            $product = [
                'type' => 'variant',
                'id'   => $variant_id,
            ];

            Cache::put(self::getSkuCacheKey($store_key, $sku), $product, 60 * 24 * 7);

            return $product;
        }

        return [
            'type' => null,
            'id'   => null,
        ];
    }

    /**
     * @param string $store_key
     * @param string $sku
     *
     * @return string
     */
    public static function getSkuCacheKey(string $store_key, string $sku): string
    {
        return $store_key.'_'.$sku;
    }

    /**
     * @param array       $product
     * @param string|null $warehouse_id
     *
     * @return int
     */
    public static function getQuantity(array $product, string $warehouse_id = null): int
    {
        if (is_null($warehouse_id)) {
            return $product['quantity'];
        }

        $inventories = collect($product['inventory'])
            ->filter(function ($warehouse_inventory) use ($warehouse_id) {
                return $warehouse_inventory['warehouse_id'] === $warehouse_id;
            });

        if ($inventories->isEmpty()) {
            return $product['quantity'];
        }

        return $inventories->first()['quantity'];
    }

    /**
     * @param $product
     * @param Api2cartConnection $connection
     * @return array
     */
    private static function transformProduct($product, Api2cartConnection $connection): array
    {
        $product['sku'] = empty($product['u_sku']) ? $product['u_model'] : $product['u_sku'];
        $product['model'] = $product['u_model'];
        $product['quantity'] = self::getQuantity($product, $connection->magento_warehouse_id);

        $created_at = $product['special_price']['created_at'];
        $product['sprice_create'] = empty($created_at) ? '1900-01-01 00:00:00' : $created_at['value'];
        $product['sprice_create'] = Carbon::createFromTimeString($product['sprice_create'])->format('Y-m-d H:i:s');

        $expired_at = $product['special_price']['expired_at'];
        $product['sprice_expire'] = empty($expired_at) ? '1900-01-01 00:00:00' : $expired_at['value'];
        $product['sprice_expire'] = Carbon::createFromTimeString($product['sprice_expire'])->format('Y-m-d H:i:s');

        $product['special_price'] = $product['special_price']['value'];

        return $product;
    }
}
