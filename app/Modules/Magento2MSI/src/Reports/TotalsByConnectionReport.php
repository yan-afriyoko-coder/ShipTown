<?php

namespace App\Modules\Magento2MSI\src\Reports;

use App\Modules\Magento2MSI\src\Models\Magento2msiProduct;
use App\Modules\Reports\src\Models\Report;
use Illuminate\Support\Facades\DB;

class TotalsByConnectionReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->baseQuery = Magento2msiProduct::query()
            ->groupBy('connection_id');

        $this->fields = [
            'connection_id'             => 'connection_id',
            'record_count'              => DB::raw('count(*)'),
            'exists_in_magento_null'    => DB::raw('count(IF(exists_in_magento IS NULL, 1, NULL))'),
            'exists_in_magento_true'    => DB::raw('count(IF(exists_in_magento = 1, 1, NULL))'),
            'exists_in_magento_false'   => DB::raw('count(IF(exists_in_magento = 0, 1, NULL))'),
            'source_assigned_null'      => DB::raw('count(IF(source_assigned IS NULL, 1, NULL))'),
            'source_assigned_true'      => DB::raw('count(IF(source_assigned = 1, 1, NULL))'),
            'source_assigned_false'     => DB::raw('count(IF(source_assigned = 0, 1, NULL))'),
            'sync_required_null'        => DB::raw('count(IF(sync_required IS NULL, 1, NULL))'),
            'sync_required_true'        => DB::raw('count(IF(sync_required = 1, 1, NULL))'),
            'sync_required_false'       => DB::raw('count(IF(sync_required = 0, 1, NULL))'),
        ];
    }
}
