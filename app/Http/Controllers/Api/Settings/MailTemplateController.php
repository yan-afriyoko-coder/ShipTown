<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\MailTemplate\UpdateRequest;
use App\Http\Resources\MailTemplateResource;
use App\Models\MailTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() : AnonymousResourceCollection
    {
        $mailTemplates = MailTemplate::all();

        return MailTemplateResource::collection($mailTemplates);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  MailTemplate $mailTemplate
     * @return MailTemplateResource
     */
    public function update(UpdateRequest $request, MailTemplate $mailTemplate)
    {
        $mailTemplate->fill($request->validated());
        $mailTemplate->save();

        return MailTemplateResource::make($mailTemplate);
    }
}
