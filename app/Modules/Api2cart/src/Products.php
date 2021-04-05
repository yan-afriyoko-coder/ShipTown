<?php

namespace App\Modules\Api2cart\src;

use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Products extends Entity
{
    private const PRODUCT_ALLOWED_KEYS = [
        "id",
        "sku",
        "model",
        "name",
        "description",
        "price",
        "special_price",
        "sprice_create",
        "sprice_expire",
        "quantity",
        "in_stock",
        "store_id",
        "warehouse_id",
    ];

    private const PRODUCT_DONT_UPDATE_KEYS = [
        "model",
        "sku",
        "name",
        "description"
    ];

    /**
     * @param string $store_key
     * @param array $params
     * @return RequestResponse
     * @throws RequestException
     */
    public static function getProductList(string $store_key, array $params): RequestResponse
    {
        return  Client::GET($store_key, 'product.list.json', $params);
    }

    /**
     * @param string $store_key
     * @param string $sku
     * @param int|null $store_id
     * @param array|null $fields
     * @return array|null
     * @throws RequestException
     */
    public static function getSimpleProductInfo(
        string $store_key,
        string $sku,
        int $store_id = null,
        array $fields = null
    ): ?array {
        $product_id = Products::getSimpleProductID($store_key, $sku);

        if (empty($product_id)) {
            return null;
        }

        $params = [
            "id" => $product_id,
            "params" => implode(
                ",",
                $fields ?? [
                    "id",
                    "model",
                    "u_model",
                    "sku",
                    "u_sku",
                    "price",
                    "special_price",
                    "stores_ids",
                    "quantity",
                    "inventory"
                ]
            ),
        ];

        if ($store_id) {
            $params["store_id"] = $store_id;
        }

        $response =  Client::GET($store_key, 'product.info.json', $params);

        if ($response->isNotSuccess()) {
            return null;
        }

        $product = $response->getResult();

        $warehouse_id = null;

        $product["type"]            = "product";
        $product["sku"]             = empty($product["u_sku"]) ? $product["u_model"] : $product["u_sku"];
        $product["model"]           = $product["u_model"];
        $product["quantity"]        = self::getQuantity($product, $warehouse_id);

        $created_at = $product["special_price"]["created_at"];
        $product["sprice_create"]   = empty($created_at) ? "1900-01-01 00:00:00" : $created_at["value"];
        $product["sprice_create"] = Carbon::createFromTimeString($product["sprice_create"])->format("Y-m-d H:i:s");

        $expired_at = $product["special_price"]["expired_at"];
        $product["sprice_expire"]   = empty($expired_at) ? "1900-01-01 00:00:00" : $expired_at["value"];
        $product["sprice_expire"] = Carbon::createFromTimeString($product["sprice_expire"])->format("Y-m-d H:i:s");

        $product["special_price"]   = $product["special_price"]["value"];

        return $product;
    }

    /**
     * @param Api2cartConnection $conn
     * @param string $sku
     * @param array|null $fields
     * @return array|null
     * @throws RequestException
     */
    public static function getSimpleProductInfoNew(Api2cartConnection $conn, string $sku, array $fields = null): ?array
    {
        $product_id = Products::getSimpleProductID($conn->bridge_api_key, $sku);

        if (empty($product_id)) {
            return null;
        }

        $params = [
            "id" => $product_id,
            "params" => implode(
                ",",
                $fields ?? [
                    "id",
                    "model",
                    "u_model",
                    "sku",
                    "u_sku",
                    "price",
                    "special_price",
                    "stores_ids",
                    "quantity",
                    "inventory"
                ]
            ),
        ];

        if ($conn->magento_store_id) {
            $params["store_id"] = $conn->magento_store_id;
        }

        $response =  Client::GET($conn->bridge_api_key, 'product.info.json', $params);

        if ($response->isNotSuccess()) {
            return null;
        }

        $product = $response->getResult();

        $product["type"]            = "product";
        $product["sku"]             = empty($product["u_sku"]) ? $product["u_model"] : $product["u_sku"];
        $product["model"]           = $product["u_model"];
        $product["quantity"]        = self::getQuantity($product, $conn->magento_warehouse_id);

        $created_at = $product["special_price"]["created_at"];
        $product["sprice_create"]   = empty($created_at) ? "1900-01-01 00:00:00" : $created_at["value"];
        $product["sprice_create"] = Carbon::createFromTimeString($product["sprice_create"])->format("Y-m-d H:i:s");

        $expired_at = $product["special_price"]["expired_at"];
        $product["sprice_expire"]   = empty($expired_at) ? "1900-01-01 00:00:00" : $expired_at["value"];
        $product["sprice_expire"] = Carbon::createFromTimeString($product["sprice_expire"])->format("Y-m-d H:i:s");

        $product["special_price"]   = $product["special_price"]["value"];

        return $product;
    }

    /**
     * @param string $store_key
     * @param string $sku
     * @param int|null $store_id
     * @param array|null $fields
     * @return array|null
     * @throws RequestException
     */
    public static function getVariantInfo(
        string $store_key,
        string $sku,
        int $store_id = null,
        array $fields = null
    ): ?array {
        $variant_id = Products::getVariantID($store_key, $sku);

        if (empty($variant_id)) {
            return null;
        }

        $params = [
            "id" => $variant_id,
            "params" => implode(
                ",",
                $fields ?? [
                    "id",
                    "model",
                    "u_model",
                    "sku",
                    "u_sku",
                    "price",
                    "special_price",
                    "stores_ids",
                    "quantity",
                    "inventory"
                ]
            ),
        ];

        if ($store_id) {
            $params["store_id"] = $store_id;
        }

        $response =  Client::GET($store_key, 'product.variant.info.json', $params);

        if ($response->isNotSuccess()) {
            return null;
        }

        $variant = $response->getResult()["variant"];

        $variant['type']            = "variant";

        $warehouse_id = null;

        $variant["sku"]             = empty($variant["u_sku"]) ? $variant["u_model"] : $variant["u_sku"];
        $variant["model"]           = $variant["u_model"];
        $product["quantity"]        = $warehouse_id ? $variant["inventory"][0]['quantity'] : $variant["quantity"];

        $created_at = $variant["special_price"]["created_at"];
        $variant["sprice_create"]   = empty($created_at) ? "1900-01-01 00:00:00":$created_at["value"];
        $variant["sprice_create"] = Carbon::createFromTimeString($variant["sprice_create"])->format("Y-m-d H:i:s");

        $expired_at = $variant["special_price"]["expired_at"];
        $variant["sprice_expire"]   = empty($expired_at) ? "1900-01-01 00:00:00":$expired_at["value"];
        $variant["sprice_expire"] = Carbon::createFromTimeString($variant["sprice_expire"])->format("Y-m-d H:i:s");

        $variant["special_price"]   = $variant["special_price"]["value"];

        return $variant;
    }

    /**
     * @param string $store_key
     * @param string $sku
     * @param int|null $store_id
     * @param array|null $fields
     * @return array|null
     * @throws RequestException
     */
    public static function getVariantInfoNew(
        Api2cartConnection $connection,
        string $sku,
        array $fields = null
    ): ?array {
        $variant_id = Products::getVariantID($connection->bridge_api_key, $sku);

        if (empty($variant_id)) {
            return null;
        }

        $params = [
            "id" => $variant_id,
            "params" => implode(
                ",",
                $fields ?? [
                    "id",
                    "model",
                    "u_model",
                    "sku",
                    "u_sku",
                    "price",
                    "special_price",
                    "stores_ids",
                    "quantity",
                    "inventory"
                ]
            ),
        ];

        if ($connection->magento_store_id) {
            $params["store_id"] = $connection->magento_store_id;
        }

        $response =  Client::GET($connection->bridge_api_key, 'product.variant.info.json', $params);

        if ($response->isNotSuccess()) {
            return null;
        }

        $variant = $response->getResult()["variant"];

        $variant['type']            = "variant";

        $variant["sku"]             = empty($variant["u_sku"]) ? $variant["u_model"] : $variant["u_sku"];
        $variant["model"]           = $variant["u_model"];
        $variant["quantity"]        = self::getQuantity($variant, $connection->magento_warehouse_id);

        $created_at = $variant["special_price"]["created_at"];
        $variant["sprice_create"]   = empty($created_at) ? "1900-01-01 00:00:00":$created_at["value"];
        $variant["sprice_create"] = Carbon::createFromTimeString($variant["sprice_create"])->format("Y-m-d H:i:s");

        $expired_at = $variant["special_price"]["expired_at"];
        $variant["sprice_expire"]   = empty($expired_at) ? "1900-01-01 00:00:00":$expired_at["value"];
        $variant["sprice_expire"] = Carbon::createFromTimeString($variant["sprice_expire"])->format("Y-m-d H:i:s");

        $variant["special_price"]   = $variant["special_price"]["value"];

        return $variant;
    }

    /**
     * @param string $store_key
     * @param string $sku
     * @param int|null $store_id
     * @param array|null $fields
     * @return array|null
     * @throws RequestException
     */
    public static function getProductInfo(string $store_key, string $sku, int $store_id = null, array $fields = ['force_all'])
    {
        $product = Products::getSimpleProductInfo($store_key, $sku, $store_id, $fields);

        if ($product) {
            return $product;
        }

        $variant = Products::getVariantInfo($store_key, $sku, $store_id, $fields);

        if ($variant) {
            return $variant;
        }

        return null;
    }

    /**
     * @param Api2cartConnection $connection
     * @param string $sku
     * @param array|null $fields
     * @return array|null
     * @throws RequestException
     */
    public static function getProductInfoNew(Api2cartConnection $connection, string $sku, array $fields = null): ?array
    {
        $product = Products::getSimpleProductInfoNew($connection, $sku, $fields);

        if ($product) {
            return $product;
        }

        $variant = Products::getVariantInfoNew($connection, $sku, $fields);

        if ($variant) {
            return $variant;
        }

        return null;
    }

    /**
     * @param string $store_key
     * @param string $sku
     * @return int|null
     * @throws RequestException
     */
    public static function getSimpleProductID(string $store_key, string $sku)
    {
        $cache_key = $store_key."_".$sku."_product_id";

        $id = Cache::get($cache_key);

        if ($id) {
            return $id;
        }

        try {
            $response =  Client::GET($store_key, 'product.find.json', [
                'find_where' => "model",
                'find_value' => $sku,
                'store_id' => 0
            ]);
        } catch (Exception $exception) {
            if ($exception instanceof RequestException && $exception->getCode() === RequestResponse::RETURN_CODE_MODEL_NOT_FOUND) {
                return null;
            }
            throw $exception;
        }

        if (isset($response) && ($response->isNotSuccess())) {
            return null;
        }

        $id = $response->getResult()['product'][0]["id"];

        Cache::put($cache_key, $id, 60 * 24 * 7);

        return $id;
    }

    /**
     * @param string $store_key
     * @param string $sku
     * @return int|null
     */
    public static function getVariantID(string $store_key, string $sku)
    {
        $cache_key = $store_key."_".$sku."_variant_id";

        $id = Cache::get($cache_key);

        if ($id) {
            return $id;
        }

        try {
            $response = Client::GET($store_key, 'product.child_item.find.json', [
                'find_where' => "sku",
                'find_value' => $sku
            ]);
        } catch (Exception $exception) {
            return null;
        }

        if (isset($response) && ($response->isNotSuccess())) {
            return null;
        }

        $id = $response->getResult()['children'][0]["id"];

        Cache::put($cache_key, $id, 60 * 24 * 7);

        return $id;
    }

    /**
     * @param string $store_key
     * @param array $product_data
     * @return RequestResponse
     * @throws Exception
     */
    public static function createSimpleProduct(string $store_key, array $product_data): RequestResponse
    {
        $fields = [
            "sku",
            "model",
            "name",
            "description",
            "price"
        ];

        $product = Arr::only($product_data, $fields);

        if (!Arr::has($product_data, "model")) {
            $product["model"] = $product_data["sku"];
        }

        // disable new products
        $product["available_for_view"] = false;
        $product["available_for_sale"] = false;

        $response = Client::POST($store_key, 'product.add.json', $product);

        if ($response->isSuccess()) {
            Log::info('Product created', $product_data);
            return $response;
        }

        Log::error("product.add.json failed", $response->asArray());
        return $response;
    }

    /**
     * @param string $store_key
     * @param array $product_data
     * @return RequestResponse
     * @throws Exception
     */
    public static function updateSimpleProduct(string $store_key, array $product_data): RequestResponse
    {
        $product = Arr::only($product_data, self::PRODUCT_ALLOWED_KEYS);

        $product = Arr::except($product, self::PRODUCT_DONT_UPDATE_KEYS);

        $response = Client::GET($store_key, 'product.update.json', $product);

        if ($response->isSuccess()) {
            logger("Product updated", $product);
            return $response;
        }

        switch ($response->getReturnCode()) {
            case RequestResponse::RETURN_CODE_MODEL_NOT_FOUND:
                Products::assignStore($store_key, $product_data['id'], $product_data['store_id']);
                return self::updateSimpleProduct($store_key, $product_data);
                break;
        }

        Log::error('product.update.json failed', $response->asArray());
        return $response;
    }

    /**
     * @param string $store_key
     * @param int $product_id
     * @param int $store_id
     * @return RequestResponse
     * @throws Exception
     */
    public static function assignStore(string $store_key, int $product_id, int $store_id): RequestResponse
    {
        $data = [
            "product_id" => $product_id,
            "store_id" => $store_id
        ];

        $response = Client::POST($store_key, 'product.store.assign.json', $data);

        if ($response->isSuccess()) {
            Log::info('Store assigned', $data);
            return $response;
        }

        Log::error('product.store.assign.json failed', $response->asArray());
        return $response;
    }

    /**
     * This will only update variant product, will not update simple product
     * @param string $store_key
     * @param array $variant_data
     * @return RequestResponse
     * @throws Exception
     */
    public static function updateVariant(string $store_key, array $variant_data): RequestResponse
    {
        $properties = Arr::only($variant_data, self::PRODUCT_ALLOWED_KEYS);

        $properties = Arr::except($properties, self::PRODUCT_DONT_UPDATE_KEYS);

        $response = Client::GET($store_key, 'product.variant.update.json', $properties);

        if ($response->isSuccess()) {
            logger("Variant updated", $properties);
            return $response;
        }

        Log::error('product.variant.update.json failed', $response->asArray());
        return $response;
    }

    /**
     * @param string $store_key
     * @param array $product_data
     * @return RequestResponse
     * @throws Exception
     */
    public static function update(string $store_key, array $product_data): RequestResponse
    {
        $product = Products::getProductTypeAndId($store_key, $product_data['sku']);

        switch ($product["type"]) {
            case "product":
                $properties = array_merge($product_data, ['id' => $product["id"]]);
                return Products::updateSimpleProduct($store_key, $properties);
                break;
            case "variant":
                $properties = array_merge($product_data, ['id' => $product["id"]]);
                return Products::updateVariant($store_key, $properties);
                break;
            default:
                logger('Cannot update - SKU not found', [$product_data['sku']]);
                break;
        }
    }

    /**
     * @param string $store_key
     * @param array $product_data
     * @return RequestResponse
     * @throws Exception
     */
    public static function updateOrCreate(string $store_key, array $product_data): RequestResponse
    {
        $product = Products::getProductTypeAndId($store_key, $product_data['sku']);

        switch ($product["type"]) {
            case "product":
                $properties = array_merge($product_data, ['id' => $product["id"]]);
                return Products::updateSimpleProduct($store_key, $properties);
                break;
            case "variant":
                $properties = array_merge($product_data, ['id' => $product["id"]]);
                return Products::updateVariant($store_key, $properties);
                break;
            default:
                return Products::createSimpleProduct($store_key, $product_data);
                break;
        }
    }

    /**
     * @param string $store_key
     * @param string $sku
     * @return array
     * @throws RequestException
     */
    private static function getProductTypeAndId(string $store_key, string $sku): array
    {
        $cached_product = Cache::get(self::getSkuCacheKey($store_key, $sku));

        if ($cached_product) {
            return $cached_product;
        }

        $product_id = Products::getSimpleProductID($store_key, $sku);

        if (!empty($product_id)) {
            $product = [
                "type" => "product",
                "id" => $product_id
            ];

            Cache::put(self::getSkuCacheKey($store_key, $sku), $product, 60 * 24 * 7);

            return $product;
        }

        $variant_id = Products::getVariantID($store_key, $sku);

        if (!empty($variant_id)) {
            $product = [
                "type" => "variant",
                "id" => $variant_id
            ];

            Cache::put(self::getSkuCacheKey($store_key, $sku), $product, 60 * 24 * 7);

            return $product;
        }

        return [
            "type" => null,
            "id" => null
        ];
    }

    /**
     * @param string $store_key
     * @param string $sku
     * @return string
     */
    public static function getSkuCacheKey(string $store_key, string $sku): string
    {
        return $store_key . "_" . $sku;
    }

    /**
     * @param array $product
     * @param string|null $warehouse_id
     * @return int
     */
    public static function getQuantity(array $product, string $warehouse_id = null): int
    {
        if ($warehouse_id) {
            return $product["inventory"][0]['quantity'];
        }

        return $product["quantity"];
    }
}
