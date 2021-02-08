<?php


namespace App\Modules\DpdIreland\src;


use Illuminate\Support\Collection;
use Spatie\ArrayToXml\ArrayToXml;

/**
 * Class Shipment
 * @package App\Modules\Dpd\src
 */
class Address
{
    /**
     * @var array
     */
    private $templateArray = [
        'Contact' => '',
        'ContactTelephone' => '',
        'ContactEmail' => '',
        'BusinessName' => '',
        'AddressLine1' => '',
        'AddressLine2' => '',
        'AddressLine3' => '',
        'AddressLine4' => '',
        'CountryCode' =>  '',
    ];

    /**
     * @var Collection
     */
    private $template;

    /**
     * @var Collection
     */
    private $address;


    /**
     * Shipment constructor.
     * @param array $address
     */
    public function __construct(array $address)
    {
        $this->address = collect($address);
        $this->template = collect($this->templateArray);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->template->merge($this->address)
            ->only($this->template->keys())
            ->toArray();
    }
}
