<?php


namespace App\Modules\DpdIreland\src\Responses;


use Illuminate\Support\Str;
use SimpleXMLElement;

/**
 * Class PreAdviceResponse
 * @package App\Modules\Dpd\src\Responses
 */
abstract class XmlResponse
{
    /**
     * @var string
     */
    private $xmlResponse;
    /**
     * @var SimpleXMLElement|string
     */
    private $simpleXml;

    /**
     * PreAdviceResponse constructor.
     * @param string $xmlResponse
     */
    public function __construct(string $xmlResponse)
    {
        $this->xmlResponse = $xmlResponse;

        $this->simpleXml = simplexml_load_string($xmlResponse);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->xmlResponse;
    }

    /**
     * @param $key
     * @return string
     */
    public function getAttribute($key): string
    {
        $startTag = "<$key>";
        $endTag = "</$key>";

        return Str::before(Str::after($this->xmlResponse, $startTag), $endTag);
    }
}
