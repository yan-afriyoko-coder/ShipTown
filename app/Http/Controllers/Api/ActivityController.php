<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidSelectException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ActivityIndexRequest;
use App\Http\Requests\Api\ActivityStoreRequest;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\LogResource;
use App\Modules\Reports\src\Models\ActivityReport;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 *
 */
class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ActivityIndexRequest $request
     *
     * @return AnonymousResourceCollection
     * @throws ContainerExceptionInterface
     * @throws InvalidSelectException
     * @throws NotFoundExceptionInterface
     */
    public function index(ActivityIndexRequest $request): AnonymousResourceCollection
    {
        $report = new ActivityReport();

        return LogResource::collection($this->getPaginatedResult($report->queryBuilder()));
    }

    /**
     * @param ActivityStoreRequest $request
     * @return AnonymousResourceCollection
     */
    public function store(ActivityStoreRequest $request): AnonymousResourceCollection
    {
        $modelClass = 'App\\Models\\' . Str::ucfirst($request->validated()['subject_type']);

        $model = app($modelClass)->findOrFail($request->validated()['subject_id']);

        $activity = activity()
            ->on($model)
            ->log($request->validated()['description']);

        return ActivityResource::collection([$activity]);
    }
}
