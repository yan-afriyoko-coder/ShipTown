<?php


namespace App\Modules\Dpd\src;

use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Cache;

class Dpd
{
    const AUTHORIZATION_CACHE_KEY = 'dpd.authorization';
    const HTTPS_PRE_PROD_PAPI_DPD_IE = 'https://pre-prod-papi.dpd.ie';

    /**
     * @return string
     */
    private static function getBaseUrl(): string
    {
        return self::HTTPS_PRE_PROD_PAPI_DPD_IE;
    }

    public static function getPreAdvice()
    {
        $authorization = self::getAuthorization();

        return null;
    }

    /**
     * @return array
     */
    public static function getAuthorization(): array
    {
        $cachedAuthorization = Cache::get('' . self::AUTHORIZATION_CACHE_KEY . '');

        if($cachedAuthorization) {
            $cachedAuthorization['from_cache'] = true;
            return $cachedAuthorization;
        }

        $authorization = self::authorize();

        Cache::put(self::AUTHORIZATION_CACHE_KEY, $authorization,86400);

        return $authorization;
    }

    /**
     * @return GuzzleClient
     */
    public static function getGuzzleClient(): GuzzleClient
    {
        return new GuzzleClient([
            'base_uri' => self::getBaseUrl(),
            'timeout' => 60,
            'exceptions' => true,
        ]);
    }

    /**
     * @return array
     */
    private static function authorize(): array
    {
        $body = [
            'User' => config('dpd.user'),
            'Password' => config('dpd.password'),
            'Type' => 'CUST',
        ];

        $headers = [
            'Authorization' => 'Bearer ' . config('dpd.token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $authorizationResponse = self::getGuzzleClient()->post('/common/api/authorize', [
            'headers' => $headers,
            'json' => $body
        ]);

        return [
            'from_cache' => false,
            'authorization_time' => Carbon::now(),
            'authorization_response' => json_decode($authorizationResponse->getBody()->getContents(), true),
        ];
    }
}
