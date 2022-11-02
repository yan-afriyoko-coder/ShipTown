<?php

namespace App\Modules\MagentoApi\src\Api;

use Grayloon\Magento\Api\AbstractApi;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class BaseApi extends AbstractApi
{
    protected function get($path, $parameters = []): Response
    {
        $response = parent::get($path, $parameters);
        Log::debug('MAGENTO2API GET: '.$path, ['response' => $response->json(), 'params' => $parameters]);
        return $response;
    }

    protected function post($path, $parameters = []): Response
    {
        $response = parent::post($path, $parameters);
        Log::debug('MAGENTO2API POST: '.$path, ['response' => $response->json(), 'params' => $parameters]);
        return $response;
    }

    protected function put($path, $parameters = [])
    {
        $response = parent::put($path, $parameters);
        Log::debug('MAGENTO2API PUT: '.$path, ['response' => $response->json(), 'params' => $parameters]);
        return $response;
    }

    protected function delete($path, $parameters = [])
    {
        $response = parent::delete($path, $parameters);
        Log::debug('MAGENTO2API DELETE: '.$path, ['response' => $response->json(), 'params' => $parameters]);
        return $response;
    }
}
