<?php


namespace App\Services;

use App\Models\Configuration;

class AutoPilot
{
    /**
     * @return Configuration|\Illuminate\Database\Eloquent\Model|int|object
     */
    public static function getAutoPilotPackingDailyMax()
    {
        return Configuration::where('key', config('autopilot.config_key_name'))
                ->first('value') ?? 100;
    }
}
