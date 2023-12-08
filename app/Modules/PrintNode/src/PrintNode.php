<?php

namespace App\Modules\PrintNode\src;

use App\Exceptions\ShippingServiceException;
use App\Modules\PrintNode\src\Models\Client;
use App\Modules\PrintNode\src\Models\PrintJob;
use Exception;
use Illuminate\Http\Client\Response;
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
     * @return Response
     * @throws ShippingServiceException
     */
    public static function print(PrintJob $printJob): Response
    {
        $printNodeClient = self::getFirstPrintNodeClient();

        if (!$printNodeClient) {
            Log::warning('Print job failed, no PrintNode clients configured');
            throw new ShippingServiceException('PrintNode service not configured');
        }

        return $printNodeClient->postRequest('printjobs', $printJob->toPrintNodePayload());
    }

    /**
     * @throws Exception
     */
    public static function printPdfFromUrl(string $url, int $printerId): Response
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
     * @throws Exception
     */
    public static function printBase64Pdf(string $base64PdfString, int $printerId = null): Response
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
     * @return Response
     * @throws Exception
     */
    public static function printRaw(string $base64_content, int $printerId = null): Response
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
