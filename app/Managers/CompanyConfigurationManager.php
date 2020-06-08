<?php


namespace App\Managers;


use App\Exceptions\Api2CartKeyNotSetException;
use App\Models\CompanyConfiguration;
use Faker\Provider\Company;

class CompanyConfigurationManager
{
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
