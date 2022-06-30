<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\NavigationMenu\StoreRequest;
use App\Http\Requests\NavigationMenu\UpdateRequest;
use App\Http\Resources\NavigationMenuResource;
use App\Models\NavigationMenu;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NavigationMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $navigationMenu = NavigationMenu::getSpatieQueryBuilder();

        return NavigationMenuResource::collection($this->getPaginatedResult($navigationMenu));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRequest  $request
     * @return NavigationMenuResource
     */
    public function store(StoreRequest $request): NavigationMenuResource
    {
        $navigationMenu = NavigationMenu::query()->create($request->validated());

        return new NavigationMenuResource($navigationMenu);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateRequest $request
     * @param  NavigationMenu $navigationMenu
     * @return NavigationMenuResource
     */
    public function update(UpdateRequest $request, NavigationMenu $navigationMenu): NavigationMenuResource
    {
        $navigationMenu->update($request->validated());

        return new NavigationMenuResource($navigationMenu);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param NavigationMenu $navigationMenu
     * @return NavigationMenuResource
     * @throws Exception
     */
    public function destroy(NavigationMenu $navigationMenu): NavigationMenuResource
    {
        $navigationMenu->delete();

        return NavigationMenuResource::make($navigationMenu);
    }
}
