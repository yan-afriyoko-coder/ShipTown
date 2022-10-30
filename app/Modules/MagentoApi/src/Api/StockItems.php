<?php

namespace App\Modules\MagentoApi\src\Api;

use Grayloon\Magento\Api\AbstractApi;
use Illuminate\Http\Client\Response;

class StockItems extends AbstractApi
{
    public function putStockItems($sku, $params)
    {
        return $this->put('/products/'.$sku.'/stockItems/0', [
            'stockItem' => $params,
        ]);
    }

    public function getStockItems($sku): Response
    {
        return $this->get('/stockItems/'.$sku);
    }

    public function getInventorySourceItems($sku, $storeCode): Response
    {
        return $this->get('/inventory/source-items', [
            'searchCriteria' => [
                'filterGroups' => [
                    [
                        'filters' => [
                            [
                                'field' => 'sku',
                                'value' => $sku,
                                'condition_type' => 'in'
                            ]
                        ]
                    ],
                    [
                        'filters' => [
                            [
                                'field' => 'source_code',
                                'value' => $storeCode,
                                'condition_type' => 'in'
                            ]
                        ]
                    ]
                ]
            ],
        ]);
    }

    public function postInventorySourceItems($sku, $storeCode, $quantity): Response
    {
        return $this->post('/inventory/source-items', [
            'sourceItems' => [
                [
                    'source_code' => $storeCode,
                    'sku' => $sku,
                    'quantity' => $quantity,
                    'status' => 1,
                ]
            ],
        ]);
    }
}
