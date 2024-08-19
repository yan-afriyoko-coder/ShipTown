<?php

namespace Laravel\Forge;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @mixin \Laravel\Forge\Forge
 */
class ForgeManager
{
    use ForwardsCalls;

    /**
     * The Forge instance.
     *
     * @var \Laravel\Forge\Forge
     */
    protected $forge;

    /**
     * Create a new Forge manager instance.
     *
     * @param  string  $token
     * @param  \GuzzleHttp\Client|null  $guzzle
     */
    public function __construct($token, HttpClient $guzzle = null)
    {
        $this->forge = new Forge($token, $guzzle);
    }

    /**
     * Dynamically pass methods to the Forge instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->forwardCallTo($this->forge, $method, $parameters);
    }
}
