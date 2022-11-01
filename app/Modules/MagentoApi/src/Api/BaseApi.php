<?php

namespace App\Modules\MagentoApi\src\Api;

use Grayloon\Magento\Api\AbstractApi;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class BaseApi extends AbstractApi
{
    protected function get($path, $parameters = []): Response
    {
        Log::debug('MAGENTO2API GET: '.$path, $parameters);
        return parent::get($path, $parameters);
    }

    protected function post($path, $parameters = []): Response
    {
        Log::debug('MAGENTO2API POST: '.$path, $parameters);
        return parent::post($path, $parameters);
    }

    protected function put($path, $parameters = [])
    {
        Log::debug('MAGENTO2API PUT: '.$path, $parameters);
        return parent::put($path, $parameters);
    }

    protected function delete($path, $parameters = [])
    {
        Log::debug('MAGENTO2API DELETE: '.$path, $parameters);
        return parent::delete($path, $parameters);
    }
}
