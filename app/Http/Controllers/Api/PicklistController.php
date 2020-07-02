<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Picklist\StoreRequest;
use App\Http\Resources\PicklistResource;
use App\Models\Picklist;
use Illuminate\Http\Request;

class PicklistController extends Controller
{
    public function index(Request $request)
    {
        return Picklist::query()
            ->where('quantity_to_pick','>',0)
            ->orderBy('shelve_location')
            ->with('product')
            ->paginate(50);
    }

    public function store(StoreRequest $request, Picklist $picklist)
    {
        $picklist->update([
            'quantity_to_pick' => $picklist->quantity_to_pick - $request->input('quantity_picked')
        ]);

        return new PicklistResource($picklist);
    }
}
