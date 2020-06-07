<?php

namespace App\Http\Controllers;

use App\Jobs\Api2cart\ImportOrdersJob;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function importOrdersFromApi2cart()
    {
        ImportOrdersJob::dispatch();

        return $this->respond_OK_200();
    }
}
