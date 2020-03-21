<?php


namespace App\Managers;


use App\Models\CompanyConfiguration;
use Faker\Provider\Company;

class CompanyConfigurationManager
{
    /**
     * @return string|null
     */
    public static function getBridgeApiKey()
    {
        $result = CompanyConfiguration::query()->select('bridge_api_key')->firstOrCreate([]);

        return $result['bridge_api_key'];
    }
}
