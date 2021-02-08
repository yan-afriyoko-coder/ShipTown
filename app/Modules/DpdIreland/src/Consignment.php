<?php


namespace App\Modules\DpdIreland\src;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\ArrayToXml\ArrayToXml;

/**
 * Class Shipment
 * @package App\Modules\Dpd\src
 */
class Consignment
{
    const SERVICE_OPTION_COD = 1;
    const SERVICE_OPTION_PREMIUM_DOCUMENT_SERVICE = 2;
    const SERVICE_OPTION_RETURN_RECEIPT = 3;
    const SERVICE_OPTION_SWAPIT = 4;
    const SERVICE_OPTION_NORMAL = 5;

    const SERVICE_TYPE_OVERNIGHT = 1;
    const SERVICE_TYPE_SATURDAY = 2;
    const SERVICE_TYPE_TIMED = 3;
    const SERVICE_TYPE_SPECIAL = 4;
    const SERVICE_TYPE_CANCELLED_ON_ARRIVAL = 5;
    const SERVICE_TYPE_2_DAY_SERVICE = 6;

    /**
     * @var array
     */
    private $templateArray = [
        'RecordID' => 1,
        'CustomerAccount' => '',
        'TotalParcels'=> 1,
        'Relabel'=> 0,
        'ServiceOption' => self::SERVICE_OPTION_NORMAL,
        'ServiceType' => self::SERVICE_TYPE_OVERNIGHT,
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
            'Consignment' => $this->toArray()
        ];

        return ArrayToXml::convert(
            $data,
            'PreAdvice',
            true,
            'iso-8859-1',
        );
    }
    /**
     * @return string
     */
    public function toString(): string
    {
        $data = $this->toXml();

        $data = str_replace(PHP_EOL,'',$data);

        return $data;
    }

    /**
     * @return array
     */
    public function toArray(): array
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
