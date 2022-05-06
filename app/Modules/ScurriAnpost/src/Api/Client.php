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
     * @return Response
     * @throws Exception
     */
    public static function getSingleConsignment(string $consignment_id): Response
    {
        return self::authenticatedClient()
            ->get(self::fullUrl('consignment/'.$consignment_id));
    }

    /**
     * @param array $data
     * @return ConsignmentsResponse|null
     * @throws Exception
     */
    public static function createMultipleConsignments(array $data): ?ConsignmentsResponse
    {
        if (empty($data)) {
            return null;
        }

        $response = self::authenticatedClient()->post(self::fullUrl('consignments'), $data);

        return new ConsignmentsResponse($response);
    }

    /**
     * @throws Exception
     */
    public static function getDocuments(string $consignment_id): Label
    {
        $url = self::fullUrl('consignment') . '/'. $consignment_id .'/documents';
        $response = self::authenticatedClient()->get($url);
        return new Label($response);
    }

    /**
     * @param array $data
     * @return string
     * @throws Exception
     */
    public static function createSingleConsignment(array $data): string
    {
        $consignmentList = [
            0 => $data
        ];

        $response = self::createMultipleConsignments($consignmentList);

        if ($response->errors->isNotEmpty()) {
            throw new Exception($response->errors);
        }

        return $response->success[0];
    }
}
