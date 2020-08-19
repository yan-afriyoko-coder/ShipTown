<?php

namespace App\Http\Controllers;

use App\Services\PdfService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class PdfBaseController extends Controller
{
    /**
     * @param string $view
     * @param array $data
     * @return ResponseFactory|Response
     */
    public function getPdfResponse(string $view, array $data)
    {
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
        ];

        $pdfString = PdfService::fromView($view, $data);

        return response($pdfString, 200, $headers);
    }
}
