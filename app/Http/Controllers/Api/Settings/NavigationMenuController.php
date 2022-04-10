<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\NavigationMenu\StoreRequest;
use App\Http\Requests\NavigationMenu\UpdateRequest;
use App\Http\Resources\NavigationMenuResource;
use App\Models\NavigationMenu;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NavigationMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $navigationMenu = NavigationMenu::all();

        return NavigationMenuResource::collection($navigationMenu);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest  $request
     * @return NavigationMenuResource
     */
    public function store(StoreRequest $request)
    {
        $navigationMenu = NavigationMenu::create($request->validated());

        return new NavigationMenuResource($navigationMenu);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest $request
     * @param  \App\Models\NavigationMenu  $navigationMenu
     * @return NavigationMenuResource
     */
    public function update(UpdateRequest $request, NavigationMenu $navigationMenu)
    {
        $navigationMenu->update($request->validated());

        return new NavigationMenuResource($navigationMenu);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NavigationMenu  $navigationMenu
     * @return NavigationMenuResource
     */
    public function destroy(NavigationMenu $navigationMenu)
    {
        $navigationMenu->delete();

        return true;
    }
}
