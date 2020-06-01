<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return new ResourceCollection(Role::all());
        } else {

        }
    }
}
