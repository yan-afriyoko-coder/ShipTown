<?php

namespace App\Http\Controllers\Api\Reports;

use App\Modules\Reports\src\Models\PicksReport;
use Illuminate\Http\Resources\Json\JsonResource;

class PicksController
{
    public function index()
    {
        return PicksReport::toJsonResource();
    }
}
