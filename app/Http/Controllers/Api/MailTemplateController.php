<?php

namespace App\Http\Controllers\Api;

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
        $data = $request->validated();

        if (isset($data['to'])) {
            $data['to'] = implode(", ", $data['to']);
        }

        $mailTemplate->update($data);

        return MailTemplateResource::make($mailTemplate);
    }
}
