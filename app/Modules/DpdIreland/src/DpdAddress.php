<?php

namespace App\Modules\DpdIreland\src;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class Shipment.
 */
class DpdAddress
{
    /**
     * @var Collection
     */
    private $address;

    /**
     * Shipment constructor.
     *
     * @param array $address
     */
    public function __construct(array $address)
    {
        $this->address = collect($address);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'Contact'          => $this->address->get('Contact', ''),
            'ContactTelephone' => $this->address->get('ContactTelephone', ''),
            'ContactEmail'     => $this->address->get('ContactEmail', ''),
            'BusinessName'     => $this->address->get('BusinessName', ''),
            'AddressLine1'     => $this->address->get('AddressLine1', ''),
            'AddressLine2'     => $this->address->get('AddressLine2', ''),
            'AddressLine3'     => $this->address->get('AddressLine3', ''),
            'AddressLine4'     => $this->address->get('AddressLine4', ''),
            'PostCode'         => $this->getOnlyCorrectPostCode(),
            'CountryCode'      => $this->address->get('CountryCode', ''),
        ];
    }

    /**
     * @return mixed
     */
    public function getOnlyCorrectPostCode(): string
    {
        $postCode = $this->address->get('PostCode', '');

        return Str::length($postCode) != 7 ? '' : $postCode;
    }
}
