<?php


namespace App\Managers;


use App\Exceptions\Api2CartKeyNotSetException;
use App\Models\CompanyConfiguration;
use Faker\Provider\Company;

class CompanyConfigurationManager
{
    /**
     * @return string|null
     * @throws Api2CartKeyNotSetException
     */
    public static function getBridgeApiKey()
    {
        $result = CompanyConfiguration::query()->select('bridge_api_key')->firstOrCreate([],[]);

        $key = $result['bridge_api_key'];

        if(is_null($key)) {
            throw new Api2CartKeyNotSetException("Bridge API key not set");
        }

        return $key;
    }

    /**
     * @param $key
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public static function set($key, $value) {
        return CompanyConfiguration::query()->updateOrCreate([], [
            $key => $value
        ]);
    }
}
