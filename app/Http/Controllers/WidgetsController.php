<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Widget;
use App\Http\Requests\StoreWidgetRequest;
use App\Http\Requests\UpdateWidgetRequest;

class WidgetsController extends Controller
{
    /**
     * @param StoreWidgetRequest $request
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
     */
    public function update(UpdateWidgetRequest $request, Widget $widget)
    {
        $widget->fill($request->all());
        $widget->save();

        return new JsonResource($widget);
    }    
}
