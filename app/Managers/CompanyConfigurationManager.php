<?php


namespace App\Managers;


use App\Models\CompanyConfiguration;

class CompanyConfigurationManager
{
    /**
     * @return string|null
     */
    public static function getBridgeApiKey()
    {
        $result = CompanyConfiguration::query()->first('bridge_api_key');
        return $result['bridge_api_key'];
    }
}
