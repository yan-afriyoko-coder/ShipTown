<?php

namespace App\Modules\PrintNode\src;

use App\Modules\DpdIreland\src\Responses\PreAdvice;
use App\Modules\PrintNode\src\Models\Client;
use App\Modules\PrintNode\src\Models\PrintJob;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class PrintNode
{
    /**
     * Noop: Quick "ping" to see if communication and authentication works
     *
     * @param Client|null $printNodeClient
     * @return bool
     */
    public static function noop(Client $printNodeClient = null): bool
    {
        $client = $printNodeClient ?: self::getFirstPrintNodeClient();

        try {
            $response = $client->getRequest('noop');

            return $response->getStatusCode() === 200;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @return array
     */
    public static function getPrinters(): array
    {
        $printNodeClient = self::getFirstPrintNodeClient();

        if (!$printNodeClient) {
            return [];
        }

        $response = $printNodeClient->getRequest('printers');

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param PrintJob $printJob
     * @return int
     * @throws Exception
     */
    public static function print(PrintJob $printJob): int
    {
        $printNodeClient = self::getFirstPrintNodeClient();

        if (!$printNodeClient) {
            Log::warning('Print job failed, no PrintNode clients configured');
            throw new Exception('PrintNode service not configured');
        }

        $response = $printNodeClient->postRequest('printjobs', $printJob->toPrintNodePayload());

        return (int) $response->getBody()->getContents();
    }

    /**
     * @param string $url
     * @param int $printerId
     * @return int
     */
    public static function printPdfFromUrl(string $url, int $printerId): int
    {
        $printJob = new PrintJob();
        $printJob->title = 'Url Print';
        $printJob->pdf_url = $url;

        if ($printerId) {
            $printJob->printer_id = $printerId;
        } elseif (isset(auth()->user()->printer_id)) {
            $printJob->printer_id = auth()->user()->printer_id;
        }

        $printJob->save();

        return PrintNode::print($printJob);
    }

    /**
     * @param string $base64PdfString
     * @param int|null $printerId
     * @return int
     */
    public static function printBase64Pdf(string $base64PdfString, int $printerId = null): int
    {
        $printJob = new PrintJob();
        $printJob->title = 'Url Print';
        $printJob->content_type = 'pdf_base64';
        $printJob->content = $base64PdfString;

        if ($printerId) {
            $printJob->printer_id = $printerId;
        } elseif (isset(auth()->user()->printer_id)) {
            $printJob->printer_id = auth()->user()->printer_id;
        }

        $printJob->save();

        return self::print($printJob);
    }

    /**
     * @param string $base64_content
     * @param int|null $printerId
     * @return int
     */
    public static function printRaw(string $base64_content, int $printerId = null): int
    {
        $printJob = new PrintJob();
        $printJob->title = 'Raw Print';
        $printJob->content_type = 'raw_base64';
        $printJob->content = $base64_content;

        if ($printerId) {
            $printJob->printer_id = $printerId;
        } elseif (isset(auth()->user()->printer_id)) {
            $printJob->printer_id = auth()->user()->printer_id;
        }

        $printJob->save();

        return self::print($printJob);
    }

    /**
     * @return Client|null
     */
    public static function getFirstPrintNodeClient(): ?Client
    {
        $clients = Client::all();

        if ($clients->isEmpty()) {
            return null;
        }

        return $clients->first();
    }
}
