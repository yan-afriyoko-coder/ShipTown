<?php

namespace App\Http\Controllers;

use App\Services\PdfService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class PdfBaseController extends Controller
{
    /**
     * @param string $pdfString
     * @return ResponseFactory|Response
     */
    public function getPdfResponse(string $pdfString)
    {
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
        ];

        return response($pdfString, 200, $headers);
    }
}
