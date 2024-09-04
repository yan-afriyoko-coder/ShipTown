<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DataCollectionCommentStoreRequest;
use App\Http\Resources\DataCollectionCommentResource;
use App\Models\DataCollectionComment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class DataCollectionCommentController.
 *
 * @group DataCollection
 */
class DataCollectionCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): LengthAwarePaginator
    {
        $query = DataCollectionComment::getSpatieQueryBuilder();

        return $this->getPaginatedResult($query);
    }

    public function store(DataCollectionCommentStoreRequest $request): AnonymousResourceCollection
    {
        $shipment = new DataCollectionComment($request->validated());
        $shipment->user()->associate($request->user());
        $shipment->save();

        return DataCollectionCommentResource::collection(collect([$shipment]));
    }
}
