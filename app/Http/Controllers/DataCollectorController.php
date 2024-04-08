<?php

namespace App\Http\Controllers;

use App\Modules\Reports\src\Models\DataCollectionReport;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DataCollectorController extends Controller
{
    public function index(Request $request, $id): Factory|View|Response|Application|ResponseFactory
    {
        if ($request->has('filename')) {
            $report = new DataCollectionReport();
            return $report->toCsvFileDownload();
        }

        return view('data-collector-page', ['data_collection_id' => $id]);
    }
}
