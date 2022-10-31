<?php

namespace App\Modules\MagentoApi\src\Services;

use App\Modules\MagentoApi\src\Api\StockItems;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use Grayloon\Magento\Magento;
use Illuminate\Support\Facades\Log;

class MagentoService
{
    public static function fetchBasePrices(MagentoProduct $magentoProduct)
    {
        $stockItems = new StockItems(new Magento());
        $response = $stockItems->postProductsBasePricesInformation($magentoProduct->product->sku);

        if ($response->successful()) {
            $magentoProduct->base_prices_fetched_at = now();
            $magentoProduct->base_prices_raw_import = $response->json();

            collect($response->json())
                ->filter(function ($item) use ($magentoProduct) {
                    return $item['store_id'] > $magentoProduct->magentoConnection->magento_store_id;
                })
                ->each(function ($item) use ($magentoProduct) {
                    $magentoProduct->magento_price = $item['price'];
                });

            $magentoProduct->save();
        } else {
            Log::error('Failed to fetch base prices for product '.$magentoProduct->product->sku);
        }

//        self::fetchStockItem($magentoProduct);
    }

    public static function fetchInventory(MagentoProduct $magentoProduct)
    {
        if (config('magento.store_code') === 'all') {
            self::fetchStockItem($magentoProduct);
            return;
        }

        self::fetchFromInventorySourceItems($magentoProduct);
    }

    public static function updateInventory(string $sku, float $quantity)
    {
        if (config('magento.store_code') === 'all') {
            self::updateStockItems($sku, $quantity);
            return;
        }

        self::updateInventorySourceItems($sku, $quantity);
    }

    private static function updateStockItems(string $sku, float $quantity): void
    {
        $stockItems = new StockItems(new Magento());

        $params = [
            'is_in_stock' => $quantity > 0,
            'qty' => $quantity,
        ];

        $response = $stockItems->putStockItems($sku, $params);

        Log::debug('MagentoApi: stockItem update', [
            'sku'                  => $sku,
            'response_status_code' => $response->status(),
            'response_body'        => $response->json(),
            'params'               => $params
        ]);
    }

    private static function updateInventorySourceItems(string $sku, float $quantity): void
    {
        $stockItems = new StockItems(new Magento());

        $response = $stockItems->postInventorySourceItems($sku, config('magento.store_code'), $quantity);

        Log::debug('MagentoApi: updateInventorySourceItems', [
            'sku'                  => $sku,
            'response_status_code' => $response->status(),
            'response_body'        => $response->json(),
        ]);
    }

    private static function fetchStockItem(MagentoProduct $product)
    {
        $stockItems = new StockItems(new Magento());

        $response = $stockItems->getStockItems($product->product->sku);

        $product->update([
            'stock_items_fetched_at' => now(),
            'stock_items_raw_import' => $response->json(),
            'quantity'               => data_get($response->json(), 'qty'),
        ]);
    }

    private static function fetchFromInventorySourceItems(MagentoProduct $product)
    {
        $stockItems = new StockItems(new Magento());

        $response = $stockItems->getInventorySourceItems($product->product->sku, config('magento.store_code'));

        $product->update([
            'stock_items_fetched_at' => now(),
            'stock_items_raw_import' => data_get($response->json(), 'items.0'),
            'quantity'               => data_get($response->json(), 'items.0.quantity'),
        ]);
    }
}
