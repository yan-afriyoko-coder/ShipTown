<?php


namespace App\Modules\ScurriAnpost\src\Api;

use Exception;
use Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;

/**
 * Class Client
 * @package App\Modules\ScurriAnpost\src\Api
 */
class Client
{
    /**
     * @return PendingRequest
     * @throws Exception
     */
    private static function authenticatedClient(): PendingRequest
    {
        $username = config('scurri.username');
        $password = config('scurri.password');

        if (in_array(null, [$username, $password])) {
            throw new Exception('Invalid Credentials');
        }

        return Http::withBasicAuth($username, $password);
    }

    /**
     * @param string $endpoint
     * @return string
     */
    private static function fullUrl(string $endpoint): string
    {
        $base_uri = config('scurri.base_uri');
        $company_slug = config('scurri.company_slug');

        return $base_uri . $company_slug . '/' .$endpoint;
    }

    /**
     * @return Response
     * @throws Exception
     */
    public static function getCarriers(): Response
    {
        return self::authenticatedClient()
            ->get(self::fullUrl('carriers'));
    }

    /**
     * @param array $data
     * @return Consignment
     * @throws Exception
     */
    public static function createConsignment(array $data): Consignment
    {
        $consignmentList = [];
        $consignmentList[] = $data;

        $response = self::authenticatedClient()->post(self::fullUrl('consignments'), $consignmentList);

        return new Consignment($response);
    }

    /**
     * @throws Exception
     */
    public static function getDocuments(Consignment $consignment): Label
    {
        $url = self::fullUrl('consignment') . '/'. $consignment->consignmentId() .'/documents';
        $response = self::authenticatedClient()->get($url);
        return new Label($response);
    }
}
