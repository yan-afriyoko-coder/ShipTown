<?php

namespace App\Services;

use PrintNode\Printer;
use PrintNode\PrintJob;
use PrintNode\Request;
use PrintNode\Response;

/**
 * Class PrintService
 * @package App\Services
 */
class PrintService
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @return PrintService
     */
    public static function print(): PrintService
    {
        return app(PrintService::class);
    }

    /**
     * PrintService constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Printer[]|null
     */
    public function getPrinters(): ?array
    {
        return $this->request->getPrinters();
    }

    /**
     * @param $printerId
     * @param string $title
     * @param string $content
     * @return Response|null
     */
    public function newPdfPrintJob($printerId, string $title, string $content): ?Response
    {
        if (@is_file($content)) { // if file exists and is a file and not a directory
            $content = file_get_contents($content);
        }

        return $this->newPrintJob($printerId, $title, base64_encode($content));
    }

    /**
     * @param $printerId
     * @param string $title
     * @param string $url
     * @return Response|null
     */
    public function printPdfFromUrl($printerId, string $title, string $url): ?Response
    {
        return $this->newPrintJob($printerId, $title, $url, 'pdf_uri');
    }

    /**
     * @param $printerId
     * @param string $title
     * @param string $content
     * @param string $contentType
     * @return Response|null
     */
    public function newPrintJob($printerId, string $title, string $content, $contentType = 'pdf_base64'): ?Response
    {
        $printJob = new PrintJob();
        $printJob->printer = $printerId;
        $printJob->contentType = $contentType;
        $printJob->content = $content;
        $printJob->source = env('APP_NAME');
        $printJob->title = $title;
        $printJob->options = [
            'paper' => 'User defined',
            'fit_to_page' => false
        ];

        return $this->request->post($printJob);
    }
}
