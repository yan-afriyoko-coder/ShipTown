<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrintDpdLabelStoreRequest;
use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Consignment;

/**
 * Class PrintDpdLabelController
 * @package App\Http\Controllers\Api
 */
class PrintDpdLabelController extends Controller
{
    /**
     * @param PrintDpdLabelStoreRequest $request
     * @return void
     */
    public function store(PrintDpdLabelStoreRequest $request, $order_number)
    {
        $consignment = new Consignment([]);

        Dpd::getPreAdvice($consignment);

        return $this->respondOK200();
    }
}
