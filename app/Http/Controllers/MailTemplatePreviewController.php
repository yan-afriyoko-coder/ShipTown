<?php

namespace App\Http\Controllers;

use App\Models\MailTemplate;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 *
 */
class MailTemplatePreviewController extends Controller
{
    /**
     * @param Request $request
     * @param MailTemplate $mailTemplate
     * @return Application|ResponseFactory|Response
     */
    public function index(Request $request, MailTemplate $mailTemplate)
    {
        return response($mailTemplate->html_template);
    }
}
