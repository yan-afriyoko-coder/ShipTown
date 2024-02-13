<?php

namespace App\Http\Controllers\Api\Modules\Magento2msi;

use App\Http\Controllers\Controller;
use App\Modules\Magento2MSI\src\Models\Magento2msiConnection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Magento2MsiConnectionController extends Controller
{
    public function index(Request $request)
    {
        return JsonResource::collection(Magento2msiConnection::getSpatieQueryBuilder()->get());
    }

    public function store(Request $request)
    {
        return Magento2msiConnection::create($request->all());
    }
}
