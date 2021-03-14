<?php

namespace App\Modules\PrintNode\src\Models;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Database\Eloquent\Model;
use Psr\Http\Message\ResponseInterface;

class Client extends Model
{
    protected $table = 'module_printnode_clients';

    protected $fillable = ['api_key'];

    /**
     * @var GuzzleClient
     */
    private $guzzleClient;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->guzzleClient = new GuzzleClient([
            'base_uri' => 'https://api.printnode.com',
            'timeout' => 60,
            'exceptions' => true,
        ]);
    }

    /**
     * @param string $uri
     * @return ResponseInterface
     */
    public function getRequest(string $uri): ResponseInterface
    {
        return $this->guzzleClient->get($uri, ['headers' => $this->generateHeaders()]);
    }

    /**
     * @param string $uri
     * @param array $json
     * @return ResponseInterface
     */
    public function postRequest(string $uri, array $json): ResponseInterface
    {
        return $this->guzzleClient->post($uri, [
            'headers' => $this->generateHeaders(),
            'json' => $json
        ]);
    }

    public function generateHeaders(): array
    {
        return  [
            'Authorization' => 'Basic ' . base64_encode($this->api_key),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
