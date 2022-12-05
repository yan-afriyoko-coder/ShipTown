<?php

namespace App\Modules\PrintNode\src\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Database\Eloquent\Model;
use Psr\Http\Message\ResponseInterface;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class Client.
 *
 * @property int                             $id
 * @property string                          $api_key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Client extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'modules_printnode_clients';

    /**
     * @var string[]
     */
    protected $fillable = ['api_key'];

    /**
     * @var GuzzleClient
     */
    private $guzzleClient;

    /**
     * Client constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->guzzleClient = new GuzzleClient([
            'base_uri'   => 'https://api.printnode.com',
            'timeout'    => 60,
            'exceptions' => true,
        ]);
    }

    /**
     * @param string $uri
     *
     * @return ResponseInterface
     */
    public function getRequest(string $uri): ResponseInterface
    {
        return $this->guzzleClient->get($uri, ['headers' => $this->generateHeaders()]);
    }

    /**
     * @param string $uri
     * @param array  $json
     *
     * @return ResponseInterface
     */
    public function postRequest(string $uri, array $json): ResponseInterface
    {
        return $this->guzzleClient->post($uri, [
            'headers' => $this->generateHeaders(),
            'json'    => $json,
        ]);
    }

    /**
     * @return string[]
     */
    public function generateHeaders(): array
    {
        return  [
            'Authorization' => 'Basic '.base64_encode($this->api_key),
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
    }

    /**
     * @return QueryBuilder
     */
    public static function getSpatieQueryBuilder(): QueryBuilder
    {
        return QueryBuilder::for(Client::class);
    }
}
