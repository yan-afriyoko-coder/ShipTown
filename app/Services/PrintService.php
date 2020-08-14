<?php

namespace App\Services;

use PrintNode\Credentials;
use PrintNode\PrintJob;
use PrintNode\Request;
use PrintNode\Response;

class PrintService
{
    private $credentials = null;

    public function setApiKey($key)
    {
        $this->credentials = (new Credentials)->setApiKey($key);
    }

    /**
     * @return Array
     */
    public function getPrinters()
    {
        return $this->request()->getPrinters();
    }

    /**
     * Sends a new print job request to the service from a PDF source.
     * 
     * @param int|string $printerId The id of the printer you wish to print to.
     * @param string $title A title to give the print job. This is the name which will appear in the operating system's print queue.
     * @param string $content The path to the pdf file, a pdf string
     * 
     * @return Response
     */    
    public function newPdfPrintJob($printerId, $title, $content)
    {
        if (@is_file($content)) { // if file exists and is a file and not a directory, suppress errors
            $content = file_get_contents($content);
        }

        return $this->newPrintJob($printerId, $title, base64_encode($content));
    }

    /**
     * Sends a new print job request to the service.
     * 
     * @param int|string $printerId The id of the printer you wish to print to.
     * @param string $title A title to give the print job. This is the name which will appear in the operating system's print queue.
     * @param string $content If contentType is pdf_uri or raw_uri, this should be the URI from which the document you wish to print can be downloaded.
     *                        If contentType is pdf_base64 or raw_base64, this should be the base64-encoding of the document you wish to print.
     * 
     * @return Response
     */
    public function newPrintJob($printerId, $title, $content, $contentType = 'pdf_base64')
    {
        $printJob = new PrintJob();
        $printJob->printer = $printerId;
        $printJob->contentType = $contentType;
        $printJob->content = $content;
        $printJob->source = env('APP_NAME');
        $printJob->title = $title;

        return $this->request()->post($printJob);
    }

    private function request()
    {
        return new Request($this->credentials);
    }
}