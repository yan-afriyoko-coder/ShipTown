<?php

namespace App\Http\Controllers\Api;

use App\Mail\CustomMail;
use App\Http\Controllers\Controller;
use App\Http\Requests\MailTemplate\StoreRequest;
use App\Http\Requests\MailTemplate\UpdateRequest;
use App\Http\Requests\MailTemplateIndexRequest;
use App\Http\Resources\MailTemplateResource;
use App\Models\MailTemplate;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MailTemplateIndexRequest $request): AnonymousResourceCollection
    {
        $mailTemplates = MailTemplate::all();

        return MailTemplateResource::collection($mailTemplates);
    }

    public function store(StoreRequest $request): MailTemplateResource
    {
        $data = $request->validated();
        $data['mailable'] = CustomMail::class;
        
        if (isset($data['to'])) {
            $data['to'] = implode(", ", $data['to']);
        }

        $template = MailTemplate::create($data);
        return MailTemplateResource::make($template);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @return MailTemplateResource
     */
    public function update(UpdateRequest $request, MailTemplate $mailTemplate)
    {
        $data = $request->validated();

        if (isset($data['to'])) {
            $data['to'] = implode(', ', $data['to']);
        }

        $mailTemplate->update($data);

        return MailTemplateResource::make($mailTemplate);
    }
}
