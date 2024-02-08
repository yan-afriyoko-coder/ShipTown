<?php

namespace App\Http\Controllers\Api\Modules\StocktakeSuggestions;

use App\Models\StocktakeSuggestion;
use Illuminate\Http\Resources\Json\JsonResource;

class Configuration
{
    public function index(): JsonResource
    {
        return JsonResource::make(StocktakeSuggestion::first());
    }

    public function store(): JsonResource
    {
        return JsonResource::make(StocktakeSuggestion::first());
    }
}
