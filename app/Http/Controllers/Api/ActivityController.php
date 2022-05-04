<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ActivityIndexRequest;
use App\Http\Requests\Api\ActivityStoreRequest;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\LogResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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
     */
    public function index(ActivityIndexRequest $request): AnonymousResourceCollection
    {
        $query = QueryBuilder::for(Activity::class)
            ->allowedFilters([
                AllowedFilter::exact('subject_type'),
                AllowedFilter::exact('subject_id'),
                AllowedFilter::exact('description'),
            ])
            ->allowedIncludes([
                'causer',
            ])
            ->allowedSorts([
                'id',
                'created_at',
            ]);

        return LogResource::collection($this->getPaginatedResult($query));
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
