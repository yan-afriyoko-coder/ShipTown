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

        if ($response->failed()) {
            Log::error(implode(' ', [
                'MAGENTO2API GET',
                $path,
                $response->status(),
                $response->reason()
            ]), [
                'response' => implode(' ', [$response->status(), $response->reason()]),
                'url' => $this->constructRequest() . $path,
                'path' => $path,
                'json' => $response->json(),
                'parameters' => $parameters,
            ]);

            return $response;
        }

        Log::error(implode(' ', [
            'MAGENTO2API GET',
            $path,
            $response->status(),
            $response->reason()
        ]), [
            'response' => implode(' ', [$response->status(), $response->reason()]),
            'url' => $this->constructRequest() . $path,
            'path' => $path,
            'json' => $response->json(),
            'parameters' => $parameters,
        ]);

        return $response;
    }

    protected function post($path, $parameters = []): Response
    {
        $response = parent::post($path, $parameters);

        if ($response->failed()) {
            Log::error(implode(' ', [
                'MAGENTO2API POST',
                $path,
                $response->status(),
                $response->reason()
            ]), [
                'response' => implode(' ', [$response->status(), $response->reason()]),
                'url' => $this->constructRequest() . $path,
                'path' => $path,
                'json' => $response->json(),
                'parameters' => $parameters,
            ]);

            return $response;
        }

        Log::error(implode(' ', [
            'MAGENTO2API POST',
            $path,
            $response->status(),
            $response->reason()
        ]), [
            'response' => implode(' ', [$response->status(), $response->reason()]),
            'url' => $this->constructRequest() . $path,
            'path' => $path,
            'json' => $response->json(),
            'parameters' => $parameters,
        ]);

        return $response;
    }

    protected function put($path, $parameters = [])
    {
        $response = parent::put($path, $parameters);

        if ($response->failed()) {
            Log::error(implode(' ', [
                'MAGENTO2API PUT',
                $path,
                $response->status(),
                $response->reason()
            ]), [
                'response' => implode(' ', [$response->status(), $response->reason()]),
                'url' => $this->constructRequest() . $path,
                'path' => $path,
                'json' => $response->json(),
                'parameters' => $parameters,
            ]);

            return $response;
        }

        Log::error(implode(' ', [
            'MAGENTO2API PUT',
            $path,
            $response->status(),
            $response->reason()
        ]), [
            'response' => implode(' ', [$response->status(), $response->reason()]),
            'url' => $this->constructRequest() . $path,
            'path' => $path,
            'json' => $response->json(),
            'parameters' => $parameters,
        ]);

        return $response;
    }

    protected function delete($path, $parameters = [])
    {
        $response = parent::delete($path, $parameters);

        if ($response->failed()) {
            Log::error(implode(' ', [
                'MAGENTO2API DELETE',
                $path,
                $response->status(),
                $response->reason()
            ]), [
                'response' => implode(' ', [$response->status(), $response->reason()]),
                'url' => $this->constructRequest() . $path,
                'path' => $path,
                'json' => $response->json(),
                'parameters' => $parameters,
            ]);

            return $response;
        }

        Log::error(implode(' ', [
            'MAGENTO2API DELETE',
            $path,
            $response->status(),
            $response->reason()
        ]), [
            'response' => implode(' ', [$response->status(), $response->reason()]),
            'url' => $this->constructRequest() . $path,
            'path' => $path,
            'json' => $response->json(),
            'parameters' => $parameters,
        ]);

        return $response;
    }
}
