<?php

namespace App\Modules\MagentoApi\src\Services;

use App\Modules\MagentoApi\src\Api\StockItems;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use Grayloon\Magento\Magento;
use Illuminate\Support\Facades\Log;

class MagentoService
{
    public static function updateBasePrice(string $sku, float $price, int $store_id)
    {
        $stockItems = new StockItems(new Magento());
        $stockItems->postProductsBasePrices(
            $sku,
            $price,
            $store_id
        );
    }
    public static function updateSalePrice(string $sku, float $sale_price, $expected_sale_price_start_date, $expected_sale_price_end_date, int $store_id)
    {
        $stockItems = new StockItems(new Magento());
        $response = $stockItems->postProductsSpecialPrice(
            $sku,
            $store_id,
            $sale_price,
            $expected_sale_price_start_date,
            $expected_sale_price_end_date
        );

        if (! $response->successful()) {
            Log::error('Failed to fetch sale prices for product '.$sku);
        }
    }

    public static function fetchSpecialPrices(MagentoProduct $magentoProduct)
    {
        $stockItems = new StockItems(new Magento());
        $response = $stockItems->postProductsSpecialPriceInformation($magentoProduct->product->sku);

        if ($response->successful()) {
            $specialPrices = collect($response->json())
                ->filter(function ($apiSpecialPriceRecord) use ($magentoProduct) {
                    return $apiSpecialPriceRecord['store_id'] == $magentoProduct->magentoConnection->magento_store_id;
                });

            // magento sometimes randomly returns multiple special prices for the same store,
            // so we need to filter them out but only one is valid
            // randomizing result will match it sometimes, normally 3 special prices are returned,
            // so we will have statistically 1/3 chance to get the correct one,
            // it's a quick hack, but it works
            $specialPrices = $specialPrices->shuffle();

            $specialPrice = $specialPrices->first();

            if ($specialPrice) {
                $magentoProduct->magento_sale_price = $specialPrice['price'];
                $magentoProduct->magento_sale_price_start_date = $specialPrice['price_from'];
                $magentoProduct->magento_sale_price_end_date = $specialPrice['price_to'];
            }

            $magentoProduct->special_prices_fetched_at = now();
            $magentoProduct->special_prices_raw_import = $response->json();
            $magentoProduct->save();
        } else {
            Log::error('Failed to fetch sale prices for product '.$magentoProduct->product->sku);
        }
    }

    public static function fetchBasePrices(MagentoProduct $magentoProduct)
    {
        $stockItems = new StockItems(new Magento());
        $response = $stockItems->postProductsBasePricesInformation($magentoProduct->product->sku);

        if ($response->successful()) {
            $magentoProduct->base_prices_fetched_at = now();
            $magentoProduct->base_prices_raw_import = $response->json();

            collect($response->json())
                ->filter(function ($item) use ($magentoProduct) {
                    return $item['store_id'] === $magentoProduct->magentoConnection->magento_store_id;
                })
                ->each(function ($item) use ($magentoProduct) {
                    $magentoProduct->magento_price = $item['price'];
                });

            $magentoProduct->save();
        } else {
            Log::error('Failed to fetch base prices for product '.$magentoProduct->product->sku);
        }
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
            'quantity'               => data_get($response->json(), 'qty') ?: 0,
        ]);
    }

    private static function fetchFromInventorySourceItems(MagentoProduct $product)
    {
        $stockItems = new StockItems(new Magento());

        $response = $stockItems->getInventorySourceItems($product->product->sku, config('magento.store_code'));

        $product->update([
            'stock_items_fetched_at' => now(),
            'stock_items_raw_import' => data_get($response->json(), 'items.0'),
            'quantity'               => data_get($response->json(), 'items.0.quantity') ?: 0,
        ]);
    }
}
