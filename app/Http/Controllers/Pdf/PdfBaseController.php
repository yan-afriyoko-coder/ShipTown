<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use Dompdf\Dompdf;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class PdfBaseController extends Controller
{
    /**
     * @param string $view
     * @param array $data
     * @param string $filename
     * @return ResponseFactory|Response
     */
    public function getPdf(string $view, array $data, string $filename = null)
    {
        $html = view()->make($view, $data);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
        ];

        if($filename) {
            $headers['filename'] = $filename;
        }

        return response($dompdf->output(), 200, $headers);
    }
}
