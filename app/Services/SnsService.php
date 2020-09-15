<?php


namespace App\Services;

use App\Http\Controllers\SnsController;

/**
 * Class SnsService
 * @package App\Services
 */
class SnsService
{
    /**
     * @param string $topic
     * @param string $message
     */
    public static function publishNotification(string $topic, string $message)
    {
        $snsTopic = new SnsController($topic);

        $snsTopic->publish($message);
    }
}
