<?php


namespace App\Modules\PrintNode\src;

use App\Modules\PrintNode\src\Models\Client;
use App\Modules\PrintNode\src\Models\PrintJob;
use Exception;
use Illuminate\Support\Facades\Log;

class PrintNode
{
    public static function printPdfFromUrl(int $printer_id, string $url): int
    {
        $printJob = new PrintJob();
        $printJob->printer_id = $printer_id;
        $printJob->title = 'Url Print';
        $printJob->pdf_url = $url;

        return PrintNode::print($printJob);
    }

    public static function noop(Client $printNodeClient): bool
    {
        $client = $printNodeClient ?: self::getPrintNodeClient();

        try {
            $response = $client->getRequest('noop');
            return $response->getStatusCode() === 200;
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function print(PrintJob $printJob): int
    {
        $printNodeClient = self::getPrintNodeClient();

        if (!$printNodeClient) {
            Log::warning('Print job failed, no PrintNode clients configured');
            return -1;
        }

        $response = $printNodeClient->postRequest('printjobs', $printJob->toPrintNodePayload());

        return (int) $response->getBody()->getContents();
    }

    public static function getPrinters(): array
    {
        $printNodeClient = self::getPrintNodeClient();

        if (!$printNodeClient) {
            return [];
        }

        $response = $printNodeClient->getRequest('printers');

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @return Client|null
     */
    public static function getPrintNodeClient(): ?Client
    {
        $clients = Client::all();

        if ($clients->isEmpty()) {
            return null;
        }

        return $clients->first();
    }
}
