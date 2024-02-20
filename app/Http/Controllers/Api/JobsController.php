<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ManualRequestJobResource;
use App\Models\ManualRequestJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JobsController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return ManualRequestJobResource::collection(ManualRequestJob::query()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'job_id' => 'required|numeric|exists:manual_request_jobs,id',
        ]);

        $job = ManualRequestJob::findOrFail($request->get('job_id'));
        dispatch(new $job->job_class);

        return response()->json(['message' => 'Job dispatched', 'job_class' => $job->job_class]);
    }
}
