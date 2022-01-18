<?php

namespace App\Modules\DpdIreland\src;

use App\Modules\DpdIreland\src\Exceptions\ConsignmentValidationException;
use App\Modules\DpdIreland\src\Models\DpdIreland;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Spatie\ArrayToXml\ArrayToXml;

/**
 * Class Shipment.
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
    public array $rules = [
        'RecordID'                         => 'sometimes',
        'DeliveryAddress.Contact'          => 'required',
        'DeliveryAddress.ContactTelephone' => 'required',
        'DeliveryAddress.ContactEmail'     => 'sometimes',
        'DeliveryAddress.BusinessName'     => 'sometimes',
        'DeliveryAddress.AddressLine1'     => 'sometimes',
        'DeliveryAddress.AddressLine2'     => 'sometimes',
        'DeliveryAddress.AddressLine3'     => 'required',
        'DeliveryAddress.AddressLine4'     => 'required',
        'DeliveryAddress.PostCode'         => ['sometimes'],
        'DeliveryAddress.CountryCode'      => 'required|in:IE,IRL,UK,GB,CHE',
    ];

    /**
     * @var array
     */
    private array $templateArray = [
        'RecordID'                  => 1,
        'CustomerAccount'           => '',
        'TotalParcels'              => 1,
        'Relabel'                   => 0,
        'ServiceOption'             => self::SERVICE_OPTION_NORMAL,
        'ServiceType'               => self::SERVICE_TYPE_OVERNIGHT,
        'ConsignmentReference'      => '',
        // customs data
//        'ShipmentId'                => '1234567',
//        'ReceiverType'              => 'Private',
//        'ReceiverEORI'              => 'ABC123',
//        'SenderEORI'                => 'ABC123',
//        'SPRNRegNo'                 => 'Y4955191Q',
//        'ShipmentType'              => 'Merchandise',
//        'ShipmentInvoiceCurrency'   => 'EUR',
//        'ShipmentIncoterms'         => 'DAP',
//        'ShipmentParcelsWeight'     => '15',
//        'InvoiceNumber'             => '1234',
//        'FreightCost'               => '25',
//        'FreightCurrency'           => 'EUR',
        'DeliveryAddress'      => [
            'Contact'          => '',
            'ContactTelephone' => '',
            'ContactEmail'     => '',
            'BusinessName'     => '',
            'AddressLine1'     => '',
            'AddressLine2'     => '',
            'AddressLine3'     => '',
            'AddressLine4'     => '',
            'PostCode'         => '',
            'CountryCode'      => '',
        ],
        'CollectionAddress' => [
            'Contact'          => '',
            'ContactTelephone' => '',
            'ContactEmail'     => '',
            'BusinessName'     => '',
            'AddressLine1'     => '',
            'AddressLine2'     => '',
            'AddressLine3'     => '',
            'AddressLine4'     => '',
            'PostCode'         => '',
            'CountryCode'      => '',
        ],
        'References' => [
            'Reference' => [
                'ReferenceName'  => '',
                'ReferenceValue' => '',
                'ParcelNumber'   => 1,
            ],
        ],
//        'CustomsLines' => [
//            'CustomsLine' => [
//                'CommodityCode'             => '6109100010',
//                'CountryOfOrigin'           => '372',
//                'Description'               => 'Red pencils',
//                'Quantity'                  => '1',
//                'Measurement'               => '1U38',
//                'TotalLineValue'            => 123.45,
//                'TaricAdd1'                 => 'tet',
//                'TaricAdd2'                 => 'test',
//                'ExtraLicensingRequired'    => 0,
//                'Box44Lines'                => [
//                    'Box44Line' => [
//                        'Box44Code' => 'Y900',
//                        'Box44Value' => 231,
//                    ]
//                ]
//            ]
//        ],
    ];

    /**
     * @var Collection
     */
    private $payload;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var DpdIreland
     */
    private $config;

    /**
     * Shipment constructor.
     *
     * @param array $payload
     *
     * @throws ConsignmentValidationException
     * @throws ModelNotFoundException
     */
    public function __construct(array $payload)
    {
        $this->payload = collect($payload);

        $this->validator = Validator::make($payload, $this->rules);

        if ($this->validator->fails()) {
            Log::warning('Consignment validation fails', $payload);
            throw new ConsignmentValidationException($this->validator->errors());
        }

        $this->config = DpdIreland::firstOrFail();
    }

    /**
     * @return string
     */
    public function toXml(): string
    {
        $data = [
            'Consignment' => $this->toArray(),
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

        $data = str_replace(PHP_EOL, '', $data);

        return $data;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $fixedValues = collect([
            'CustomerAccount'   => $this->config->user,
            'DeliveryAddress'   => (new DpdAddress($this->payload['DeliveryAddress']))->toArray(),
            'CollectionAddress' => $this->getCollectionAddress(),
        ]);

        $template = collect($this->templateArray);

        return $template->merge($this->payload)
            ->merge($fixedValues)
            ->only($template->keys())
            ->toArray();
    }

    /**
     * @return array
     */
    private function getCollectionAddress(): array
    {
        $CollectionAddress = data_get($this->payload, 'CollectionAddress', $this->config->getCollectionAddress());

        $address = new DpdAddress($CollectionAddress);

        return $address->toArray();
    }
}
