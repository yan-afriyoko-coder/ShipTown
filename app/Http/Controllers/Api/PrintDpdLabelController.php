<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PrintDpdLabelStoreRequest;
use App\Modules\DpdIreland\Dpd;

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
        dd($request->get('order_number'));
        Dpd::getPreAdvice();

        return $this->respondOK200();
    }
}
