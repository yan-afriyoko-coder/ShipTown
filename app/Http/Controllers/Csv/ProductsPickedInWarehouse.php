<?php

namespace App\Http\Controllers\Csv;

use App\Http\Controllers\Controller;
use App\Models\Pick;
use App\Traits\CsvFileResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductsPickedInWarehouse extends Controller
{
    use CsvFileResponse;

    public function index(Request $request)
    {
        $query = Pick::select([
            'products.sku',
            'products.name',
            \DB::raw('0'),
            \DB::raw('0'),
            'picks.quantity_picked',
        ])
            ->join('products', 'products.id', '=', 'picks.product_id')
            ->whereDate('picks.created_at', '=', Carbon::today())
            ->where('quantity_picked', '>', 0);

        return $this->toCsvFileResponse($query->get(), 'warehouse_picks.csv');
    }
}
