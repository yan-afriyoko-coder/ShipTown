<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DataCollectorStoreRequest;
use App\Http\Resources\DataCollectResource;
use App\Models\DataCollectionRecord;
use Illuminate\Support\Facades\Auth;

class DataCollectorController extends Controller
{
    public function store(DataCollectorStoreRequest $request)
    {
        $attributes = $request->validated();
        $attributes['user_id'] = Auth::id();

        return DataCollectResource::make(
            DataCollectionRecord::create($attributes)
        );
    }
}
