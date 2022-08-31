<?php

namespace App\Modules\Reports\src\Models;

use App\Models\DataCollectionRecord;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;

class DataCollectionReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Data Collection Report';

        $this->baseQuery = DataCollectionRecord::query()
            ->leftJoin('products as product', 'data_collection_records.product_id', '=', 'product.id')
            ->leftJoin('inventory', function ($query) {
                return $query->on('data_collection_records.product_id', '=', 'inventory.product_id')
                    ->where('inventory.warehouse_id', Auth::user()->warehouse_id);
            });

        $this->fields = [
            'id'                    => 'data_collection_records.id',
            'product_sku'           => 'product.sku',
            'product_name'          => 'product.name',
            'quantity_requested'    => 'data_collection_records.quantity_requested',
            'quantity_scanned'      => 'data_collection_records.quantity_scanned',
            'quantity_to_scan'      => 'data_collection_records.quantity_to_scan',
            'quantity_reserved'     => 'inventory.quantity_reserved',
            'quantity_available'    => 'inventory.quantity_available',
            'quantity_incoming'     => 'inventory.quantity_incoming',
            'shelf_location'        => 'inventory.shelve_location',
        ];

        $this->casts = [
            'quantity_requested'    => 'float',
            'quantity_scanned'      => 'float',
            'quantity_to_scan'      => 'float',
            'quantity_reserved'     => 'float',
            'quantity_available'    => 'float',
            'quantity_incoming'     => 'float',
        ];

        $this->addFilter(
            AllowedFilter::callback('has_tags', function ($query, $value) {
                $query->whereHas('product', function ($query) use ($value) {
                    $query->withAllTags($value);
                });
            })
        );
    }
}
