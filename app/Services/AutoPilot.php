<?php


namespace App\Services;

use App\Models\Configuration;

class AutoPilot
{
    /**
     * @return int
     */
    public static function getBatchSize()
    {
        $config = Configuration::firstOrCreate([
                'key' => config('autopilot.config_key_name')
            ], [
                'value' => 50
            ]);

        return (int) $config->value;
    }

    public static function getMaxOrderAgeAllowed()
    {
        $config = Configuration::firstOrCreate([
            'key' => config('autopilot.key_names.max_order_age_allowed')
        ], [
            'value' => 5
        ]);

        return (int) $config->value;
    }
}
