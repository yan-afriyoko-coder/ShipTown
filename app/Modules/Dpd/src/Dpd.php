<?php


namespace App\Modules\Dpd\src;

use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Cache;

class Dpd
{
    const API_URL_LIVE = 'https://papi.dpd.ie';
    const AUTHORIZATION_CACHE_KEY = 'dpd.authorization';
    const COMMON_API_PREADVICE = '/common/api/preadvice';
    const COMMON_API_AUTHORIZE = '/common/api/authorize';

    const API_URL_TEST = 'https://pre-prod-papi.dpd.ie';

    public static function getPreAdvice()
    {
        $authorizationToken = self::getCachedAuthorization();

        $options = [
            'headers' => [
                'Authorization' => 'Bearer '. $authorizationToken['authorization_response']['AccessToken'],
                'Content-Type' => 'application/xml; charset=UTF8',
                'Accept' => 'application/xml',
            ],
            'body' => self::xmlContent()
        ];

        $response = self::getGuzzleClient()->post(self::COMMON_API_PREADVICE, $options);

        $responseContent = $response->getBody()->getContents();

        return $responseContent;
    }

    /**
     * @return array
     */
    public static function getCachedAuthorization(): array
    {
        $cachedAuthorization = Cache::get('' . self::AUTHORIZATION_CACHE_KEY . '');

        if($cachedAuthorization) {
            $cachedAuthorization['from_cache'] = true;
            return $cachedAuthorization;
        }

        $authorization = self::getAuthorization();

        Cache::put(self::AUTHORIZATION_CACHE_KEY, $authorization,86400);

        return $authorization;
    }

    /**
     * @return array
     */
    private static function getAuthorization(): array
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

        $authorizationResponse = self::getGuzzleClient()->post(self::COMMON_API_AUTHORIZE, [
            'headers' => $headers,
            'json' => $body
        ]);

        return [
            'from_cache' => false,
            'authorization_time' => Carbon::now(),
            'authorization_response' => json_decode($authorizationResponse->getBody()->getContents(), true),
        ];
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
     * @return string
     */
    private static function getBaseUrl(): string
    {
        return self::API_URL_TEST;
    }

    /**
     * @return string
     */
    private static function xmlContent(): string
    {
        return '<?xml version="1.0" encoding="iso-8859-1"?>
<PreAdvice>
	<Consignment>
		<RecordID>1</RecordID>
		<CustomerAccount>6597L3</CustomerAccount>
		<DeliveryDepot>0</DeliveryDepot>
		<Gazzed>0</Gazzed>
		<ConsignmentCreationDateTime>2021-01-31T20:00:00</ConsignmentCreationDateTime>
		<GazzType>PreAdvice</GazzType>
		<TrackingNumber>0</TrackingNumber>
		<TotalParcels>1</TotalParcels>
		<Relabel>1</Relabel>
		<ServiceOption>5</ServiceOption>
		<ServiceType>1</ServiceType>
		<DeliveryAddress>
			<Contact>Daniel Guiney</Contact>
			<ContactTelephone>12345678901</ContactTelephone>
			<ContactEmail>john.smith@ie.ie</ContactEmail>
			<BusinessName></BusinessName>
			<AddressLine1>DPD Ireland, Westmeath</AddressLine1>
			<AddressLine2>Unit 2B Midland Gateway Bus </AddressLine2>
			<AddressLine3>Kilbeggan</AddressLine3>
			<AddressLine4>Westmeath</AddressLine4>
			<CountryCode>IE</CountryCode>
		</DeliveryAddress>
		<CollectionAddress>
			<Contact>Artur Hanusek</Contact>
			<ContactTelephone>1234567890</ContactTelephone>
			<ContactEmail>dpd@ie</ContactEmail>
			<BusinessName>Depot 500</BusinessName>
			<AddressLine1>Athlone Business Park</AddressLine1>
			<AddressLine2>Dublin Road</AddressLine2>
			<AddressLine3>Athlone</AddressLine3>
			<AddressLine4>Westmeath</AddressLine4>
			<CountryCode>IE</CountryCode>
		</CollectionAddress>
		<References>
			<Reference>
				<ReferenceName>ShippingTransactionID</ReferenceName>
				<ReferenceValue>123</ReferenceValue>
				<ParcelNumber>1</ParcelNumber>
			</Reference>
		</References>
	</Consignment>
</PreAdvice>';
    }
}
