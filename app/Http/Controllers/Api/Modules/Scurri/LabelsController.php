<?php


namespace App\Http\Controllers\Api\Modules\Scurri;

use App\Http\Controllers\Controller;
use App\Modules\ScurriAnpost\src\Api\Client;
use Exception;
use Request;

class LabelsController extends Controller
{
    /**
     * @throws Exception
     */
    public function show(Request $request, string $consignment_id)
    {
        $pdfString = Client::getDocuments($consignment_id)->getLabels();

        $this->throwPdfResponse($pdfString);
    }
}
