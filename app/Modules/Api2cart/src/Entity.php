<?php

namespace App\Modules\Api2cart\src;

class Entity
{
    /**
     * @var Client
     */
    private $client;

    /**
     * Entity constructor.
     *
     * @param string $store_key
     * @param bool   $exceptions
     */
    public function __construct(string $store_key, bool $exceptions = true)
    {
        $this->client = new Client($store_key, $exceptions);
    }

    /**
     * @return Client
     */
    public function client()
    {
        return $this->client;
    }
}
