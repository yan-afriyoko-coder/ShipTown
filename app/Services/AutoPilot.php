<?php


namespace App\Services;

use App\Models\Configuration;

class AutoPilot
{
    /**
     * @return int
     */
    public static function getAutoPilotPackingDailyMax()
    {
        $config = Configuration::firstOrCreate([
                'key' => config('autopilot.config_key_name')
            ], [
                'value' => 50
            ]);

        return (int) $config->value;
    }
}
