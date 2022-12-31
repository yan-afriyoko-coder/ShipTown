<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\Permission\Models\Role;

/**
 * Class UserRoleController.
 */
class UserRoleController extends Controller
{
    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return new ResourceCollection(Role::all());
    }
}
