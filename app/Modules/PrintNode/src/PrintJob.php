<?php

namespace App\Modules\PrintNode\src;

use Psr\Http\Message\ResponseInterface;

/**
 * Class PrintJob
 *
 * https://www.printnode.com/en/docs/api/curl#printjobs
 *
 * @package App\Modules\PrintNode\src
 */
class PrintJob
{
    public $printJob = [
        'source' => 'Products Management',
        'expireAfter' => 15, // seconds
        'printerId' => 0,
        'title' => '',
        'contentType' => 'pdf_uri',
        'content' => 'Product Management',
//        'options' => [
//            'paper' => 'User defined',
//            'fit_to_page' => false
//        ]
    ];

    public function setPrinterId(string $printerId): PrintJob
    {
        $this->printJob['printerId'] = $printerId;
        return $this;
    }

    public function setTitle(string $title): PrintJob
    {
        $this->printJob['title'] = $title;
        return $this;
    }

    public function setContentType(string $contentType): PrintJob
    {
        $this->printJob['contentType'] = $contentType;
        return $this;
    }

    public function setContent(string $content): PrintJob
    {
        $this->printJob['content'] = $content;
        return $this;
    }

    public function printText(string $text, $base64_encoded = false): ResponseInterface
    {
        return $this->setContent($base64_encoded ? $text : base64_encode($text))
            ->setContentType('raw_base64')
            ->print();
    }

    public function printUrl(string $url): ResponseInterface
    {
        return $this->setContent($url)
            ->setContentType('pdf_uri')
            ->print();
    }

    public function printPdf(string $base64_pdf_string): ResponseInterface
    {
        return $this->setContentType('pdf_base64')
            ->setContent($base64_pdf_string)
            ->print();
    }

    private function print(): ResponseInterface
    {
        $printNodeClient = new Client();

        return $printNodeClient->print($this->toArray());
    }

    public function toArray(): array
    {
        return $this->printJob;
    }
}
