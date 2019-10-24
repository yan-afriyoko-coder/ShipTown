<?php


namespace App\Managers;


use App\Models\UserConfiguration;
use App\Scopes\AuthenticatedUserScope;
use Illuminate\Support\Arr;

class UserConfigurationManager
{
    public static function getValue($key, $user_id = null)
    {
        if(is_null($user_id)) {
            $user_id = auth()->id();
        }

        $config = UserConfiguration::withoutGlobalScope(AuthenticatedUserScope::class)
            ->where("user_id", $user_id)
            ->first();

        if(is_null($config)) {
            return null;
        };

        $config_array = json_decode($config->config, true);

        if(Arr::has($config_array, $key)) {
            return $config_array[$key];
        }

        return null;
    }
}
