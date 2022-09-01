<?php

namespace App\Modules\Reports\src\Models;

use App\Models\DataCollection;
use App\Models\DataCollectionRecord;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;

class DataCollectorListReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Data Collector List';
        $this->baseQuery = DataCollection::query();

        $this->fields = [
            'id'                    => 'id',
            'name'                  => 'name',
            'created_at'            => 'created_at',
            'updated_at'            => 'updated_at',
        ];

        $this->casts = [
            'id'                    => 'integer',
            'name'                  => 'string',
            'created_at'            => 'datetime',
            'updated_at'            => 'datetime',
        ];
    }
}
