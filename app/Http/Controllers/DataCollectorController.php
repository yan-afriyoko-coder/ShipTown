<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidSelectException;
use App\Modules\Reports\src\Models\DataCollectionReport;
use App\Modules\Reports\src\Models\RestockingReport;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;

class DataCollectorController extends Controller
{
    /**
     * @throws ContainerExceptionInterface
     * @throws InvalidSelectException
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function index(Request $request)
    {
//        dd($request->all());
        $report = new DataCollectionReport();
        $report->view = 'vue-page';

        return $report->response($request);
    }
}
