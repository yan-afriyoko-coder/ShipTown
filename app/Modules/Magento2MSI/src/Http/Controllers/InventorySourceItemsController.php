<?php

namespace App\Modules\Magento2MSI\src\Http\Controllers;

use App\Abstracts\ReportController;
use App\Modules\Magento2MSI\src\Models\Magento2msiProduct;
use App\Modules\Reports\src\Services\ReportService;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;

class InventorySourceItemsController extends ReportController
{
    public function index(Request $request): mixed
    {
        $query = Magento2msiProduct::query()
            ->addSelect('sku as product_sku');

        $report = ReportService::fromQuery($query)
            ->addFilter(AllowedFilter::exact('product_sku', 'sku'));

        return $report->response();
    }
}
