<?php


namespace App\Modules\DpdIreland\src\Responses;


use Illuminate\Support\Str;

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
     * PreAdviceResponse constructor.
     * @param string $xmlResponse
     */
    public function __construct(string $xmlResponse)
    {
        $this->xmlResponse = $xmlResponse;
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
