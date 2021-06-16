<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\LogResource;
use App\Models\Order;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $query = Activity::query()->where('subject_type', Order::class);

        $query = QueryBuilder::for($query)
            ->allowedFilters([
                AllowedFilter::exact('subject_type'),
                AllowedFilter::exact('subject_id', 'subject_id'),
            ])
            ->allowedIncludes([
                'causer'
            ])
            ->allowedSorts([
                'id',
                'created_at',
            ]);

        return LogResource::collection($this->getPaginatedResult($query));
    }
}
