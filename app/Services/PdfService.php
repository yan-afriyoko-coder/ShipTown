<?php

namespace App\Services;

use Dompdf\Dompdf;

class PdfService
{
    /**
     * @return Dompdf
     */
    public static function fromView(string $view, array $data, $raw = false): Dompdf|string
    {
        $html = view()->make($view, $data);

        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);
        $dompdf->render();

        if ($raw) {
            return $dompdf;
        }

        return $dompdf->output();
    }
}
