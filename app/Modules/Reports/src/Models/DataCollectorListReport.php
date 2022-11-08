<?php

namespace App\Modules\Reports\src\Models;

use App\Models\DataCollection;
use Spatie\QueryBuilder\AllowedFilter;

class DataCollectorListReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Data Collector List';
        $this->baseQuery = DataCollection::query()
            ->leftJoin('warehouses', 'warehouses.id', '=', 'data_collections.warehouse_id');

        $this->fields = [
            'id'                    => 'data_collections.id',
            'warehouse_code'        => 'warehouses.code',
            'warehouse_name'        => 'warehouses.name',
            'warehouse_id'          => 'warehouse_id',
            'type'                  => 'data_collections.type',
            'name'                  => 'data_collections.name',
            'created_at'            => 'data_collections.created_at',
            'updated_at'            => 'data_collections.updated_at',
            'deleted_at'            => 'data_collections.deleted_at',
        ];

        $this->casts = [
            'id'                    => 'integer',
            'warehouse_id'          => 'warehouse_id',
            'type'                  => 'string',
            'name'                  => 'string',
            'created_at'            => 'datetime',
            'updated_at'            => 'datetime',
            'deleted_at'            => 'datetime',

            'warehouses_code'        => 'string',
            'warehouses_name'        => 'string',
        ];

        $this->addFilter(
            AllowedFilter::callback('trashed', function ($query, $value) {
                $query->withTrashed($value);
            })
        );
    }
}
