<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        return new ResourceCollection(Role::all());
    }
}
