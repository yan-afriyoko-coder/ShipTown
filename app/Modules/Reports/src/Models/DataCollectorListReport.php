<?php

namespace App\Modules\Reports\src\Models;

use App\Models\DataCollection;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;

class DataCollectorListReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Data Collector List';

        $this->baseQuery = DataCollection::query()
            ->leftJoin('warehouses', 'warehouses.id', '=', 'data_collections.warehouse_id');

        $differences_count_subquery = DB::raw('(
                SELECT count(*)
                FROM data_collection_records
                WHERE data_collection_records.data_collection_id = data_collections.id
                AND data_collection_records.quantity_requested != (data_collection_records.total_transferred_in + data_collection_records.total_transferred_out)
            )');

        $this->fields = [
            'id'                    => 'data_collections.id',
            'warehouse_code'        => 'warehouses.code',
            'warehouse_name'        => 'warehouses.name',
            'warehouse_id'          => 'warehouse_id',
            'type'                  => 'data_collections.type',
            'name'                  => 'data_collections.name',
            'differences_count'     =>  $differences_count_subquery,
            'created_at'            => 'data_collections.created_at',
            'updated_at'            => 'data_collections.updated_at',
            'deleted_at'            => 'data_collections.deleted_at',
            'currently_running_task'=> 'data_collections.currently_running_task',
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
            'currently_running_task' => 'string',
        ];

        $this->addFilter(
            AllowedFilter::callback('archived', function ($query, $value) {
                if ($value === true) {
                    $query->withTrashed();
                }
            })
        );
    }
}
