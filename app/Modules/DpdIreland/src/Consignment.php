<?php


namespace App\Modules\DpdIreland\src;


use Illuminate\Support\Collection;
use Spatie\ArrayToXml\ArrayToXml;

/**
 * Class Shipment
 * @package App\Modules\Dpd\src
 */
class Consignment
{
    /**
     * @var array
     */
    private $templateArray = [
        'RecordID' => 1,
        'CustomerAccount' => '',
        'TotalParcels'=> 1,
        'Relabel'=> 0,
        'ServiceOption' => 0,
        'ServiceType' => 0,
        'DeliveryAddress' => [
            'Contact' => '',
            'ContactTelephone' => '',
            'ContactEmail' => '',
            'BusinessName' => '',
            'AddressLine1' => '',
            'AddressLine2' => '',
            'AddressLine3' => '',
            'AddressLine4' => '',
            'CountryCode' =>  '',
        ],
        'CollectionAddress' => [
            'Contact' => 'John ',
            'ContactTelephone' => '',
            'ContactEmail' => '',
            'BusinessName' => '',
            'AddressLine1' => '',
            'AddressLine2' => '',
            'AddressLine3' => '',
            'AddressLine4' => '',
            'CountryCode' =>  '',
        ],
        'References' => [
            'Reference' => [
                'ReferenceName' => '',
                'ReferenceValue' => '',
                'ParcelNumber' => 1,
            ]
        ]
    ];

    /**
     * @var Collection
     */
    private $consignment;

    /**
     * Shipment constructor.
     * @param array $consignment
     */
    public function __construct(array $consignment)
    {
        $this->consignment = collect($consignment);
    }

    /**
     * @return string
     */
    public function toXml(): string
    {
        $data = [
            'Consignment' => $this->getConsignmentData()
        ];

        return ArrayToXml::convert($data, 'PreAdvice', true, 'iso-8859-1');
    }

    /**
     * @return array
     */
    private function getConsignmentData(): array
    {
        $fixedValues = collect([
            'CustomerAccount' => config('dpd.user'),
            'DeliveryAddress' => (new Address($this->consignment['DeliveryAddress']))->toArray(),
            'CollectionAddress' => (new Address($this->consignment['CollectionAddress']))->toArray(),
        ]);

        $template = collect($this->templateArray);

        return $template->merge($this->consignment)
            ->merge($fixedValues)
            ->only($template->keys())
            ->toArray();
    }
}
