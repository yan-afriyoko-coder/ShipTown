<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PickRequestUpdateRequest;
use App\Models\Pick;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\QueryBuilder;

class PickController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $pick = QueryBuilder::for(Pick::class);

        return $pick->paginate()->appends($request->query());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Pick $pick
     * @return Response
     */
    public function show(Pick $pick)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PickRequestUpdateRequest $request
     * @param Pick $pick
     * @return JsonResource
     */
    public function update(PickRequestUpdateRequest $request, Pick $pick)
    {
        $picker = $request->user();

        $pick->pick($picker);

        return new JsonResource($pick);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Pick $pick
     * @return Response
     */
    public function destroy(Pick $pick)
    {
        //
    }
}
