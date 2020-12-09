<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWidgetRequest;
use App\Http\Requests\UpdateWidgetRequest;
use App\Models\Widget;
use Illuminate\Http\Resources\Json\JsonResource;

class WidgetsController extends Controller
{
    /**
     * @param StoreWidgetRequest $request
     * @return JsonResource
     */
    public function store(StoreWidgetRequest $request)
    {
        $widget = new Widget();
        $widget->fill($request->all());
        $widget->save();

        return new JsonResource($widget);
    }

    /**
     * @param UpdateWidgetRequest $request
     * @param Widget $widget
     * @return JsonResource
     */
    public function update(UpdateWidgetRequest $request, Widget $widget)
    {
        $widget->fill($request->all());
        $widget->save();

        return new JsonResource($widget);
    }
}
