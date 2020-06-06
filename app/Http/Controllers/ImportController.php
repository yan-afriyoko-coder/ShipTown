<?php

namespace App\Http\Controllers;

use App\Jobs\Api2cart\ImportOrdersFromApi2cartJob;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function importOrdersFromApi2cart()
    {
        ImportOrdersFromApi2cartJob::dispatch();

        return $this->respond_OK_200();
    }
}
