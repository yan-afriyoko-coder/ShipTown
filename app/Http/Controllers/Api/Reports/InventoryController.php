<?php

namespace App\Http\Controllers\Api\Reports;

use App\Modules\Reports\src\Models\InventoryReport;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryController
{
    public function index()
    {
        return InventoryReport::toJsonResource();
    }
}
