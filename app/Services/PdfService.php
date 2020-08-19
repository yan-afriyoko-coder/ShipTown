<?php


namespace App\Services;

use Dompdf\Dompdf;

class PdfService
{
    /**
     * @param string $view
     * @param array $data
     * @return string
     */
    public static function fromView(string $view, array $data)
    {
        $html = view()->make($view, $data);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();

        return $dompdf->output();
    }
}
