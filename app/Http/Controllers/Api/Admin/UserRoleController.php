<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\Permission\Models\Role;

/**
 * Class UserRoleController
 * @package App\Http\Controllers\Api\Admin
 */
class UserRoleController extends Controller
{
    /**
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        return new ResourceCollection(Role::all());
    }
}
