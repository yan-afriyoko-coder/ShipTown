<?php


namespace App\Modules\DpdIreland\src;


use Spatie\ArrayToXml\ArrayToXml;

/**
 * Class Shipment
 * @package App\Modules\Dpd\src
 */
class Shipment
{
    /**
     * @var array
     */
    private $shipment;

    /**
     * Shipment constructor.
     * @param array $shipment
     */
    public function __construct(array $shipment)
    {
        $this->shipment = $shipment;

        $this->shipment['Consignment']['CustomerAccount'] = config('dpd.user');
    }

    /**
     * @return string
     */
    public function toXml(): string
    {
        return ArrayToXml::convert($this->shipment, 'PreAdvice', true, 'iso-8859-1');
    }
}
