<?php

namespace App\Http\Controllers;

use App\Models\CompanyConfiguration;
use Exception;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function storeConfiguration(Request $request)
    {
        return $this->respond_OK_200();
    }
}
