<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HeartbeatResources;
use App\Models\Heartbeat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HeartbeatsController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $heartbeats = Heartbeat::expired()->limit(2)->get();

        return HeartbeatResources::collection($heartbeats);
    }
}
