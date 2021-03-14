<?php

namespace App\Services;

use App\Modules\PrintNode\src\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PrintService
 * @package App\Services
 */
class PrintService
{
    /**
     * @return array []
     */
    public function getPrinters(): array
    {
        return (new Client())->getPrinters();
    }

    /**
     * @param $printerId
     * @param string $title
     * @param string $content
     * @return ResponseInterface
     */
    public function newPdfPrintJob($printerId, string $title, string $content): ResponseInterface
    {
        if (@is_file($content)) { // if file exists and is a file and not a directory
            $content = file_get_contents($content);
        }

        return Client::newPrintJob()
            ->setPrinterId($printerId)
            ->setTitle($title)
            ->printPdf(base64_encode($content));
    }
}
