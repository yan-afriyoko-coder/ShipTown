<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidSelectException;
use App\Modules\Reports\src\Models\DataCollectionReport;
use App\Modules\Reports\src\Models\RestockingReport;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class DataCollectorController extends Controller
{
    /**
     * @throws ContainerExceptionInterface
     * @throws InvalidSelectException
     * @throws NotFoundExceptionInterface
     */
    public function index(Request $request, $id)
    {
        if ($request->has('filename')) {
            $report = new DataCollectionReport();
            return $report->csvDownload();
        }

        return view('data-collector-page', ['data_collection_id' => $id]);
    }
}
