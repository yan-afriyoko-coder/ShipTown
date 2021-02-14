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
    private $xml;

    /**
     * @var SimpleXMLElement|string
     */
    protected $simpleXmlArray;

    /**
     * PreAdviceResponse constructor.
     * @param string $responseXml
     */
    public function __construct(string $responseXml)
    {
        $this->setXml($responseXml);
    }

    /**
     * @param string $responseXml
     * @return XmlResponse
     */
    public function setXml(string $responseXml): XmlResponse
    {
        $this->xml = $responseXml;

        $this->simpleXmlArray = simplexml_load_string($this->xml);

        return $this;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->xml;
    }

    /**
     * @param $key
     * @return string
     */
    public function getAttribute($key): string
    {
        $startTag = "<$key>";
        $endTag = "</$key>";

        return Str::before(Str::after($this->xml, $startTag), $endTag);
    }

    /**
     * @return string
     */
    public function getPreAdviceErrorCode(): string
    {
        return $this->simpleXmlArray->PreAdviceErrorCode;
    }

    /**
     * @return string
     */
    public function getPreAdviceErrorDetails(): string
    {
        return $this->simpleXmlArray->PreAdviceErrorDetails;
    }
}
