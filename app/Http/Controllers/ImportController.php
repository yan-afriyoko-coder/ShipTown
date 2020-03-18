<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function importOrdersFromApi2cart()
    {
        return $this->respond_OK_200();
    }
}
