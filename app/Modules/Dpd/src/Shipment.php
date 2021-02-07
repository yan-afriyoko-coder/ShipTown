<?php


namespace App\Modules\Dpd\src;


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
    }

    /**
     * @return string
     */
    public function toXml(): string
    {
        return '<?xml version="1.0" encoding="iso-8859-1"?>
<PreAdvice>
	<Consignment>
		<RecordID>1</RecordID>
		<CustomerAccount>6597L3</CustomerAccount>
		<DeliveryDepot>0</DeliveryDepot>
		<Gazzed>0</Gazzed>
		<ConsignmentCreationDateTime>2021-01-31T20:00:00</ConsignmentCreationDateTime>
		<GazzType>PreAdvice</GazzType>
		<TrackingNumber>0</TrackingNumber>
		<TotalParcels>1</TotalParcels>
		<Relabel>1</Relabel>
		<ServiceOption>5</ServiceOption>
		<ServiceType>1</ServiceType>
		<DeliveryAddress>
			<Contact>Daniel Guiney</Contact>
			<ContactTelephone>12345678901</ContactTelephone>
			<ContactEmail>john.smith@ie.ie</ContactEmail>
			<BusinessName></BusinessName>
			<AddressLine1>DPD Ireland, Westmeath</AddressLine1>
			<AddressLine2>Unit 2B Midland Gateway Bus </AddressLine2>
			<AddressLine3>Kilbeggan</AddressLine3>
			<AddressLine4>Westmeath</AddressLine4>
			<CountryCode>IE</CountryCode>
		</DeliveryAddress>
		<CollectionAddress>
			<Contact>Artur Hanusek</Contact>
			<ContactTelephone>1234567890</ContactTelephone>
			<ContactEmail>dpd@ie</ContactEmail>
			<BusinessName>Depot 500</BusinessName>
			<AddressLine1>Athlone Business Park</AddressLine1>
			<AddressLine2>Dublin Road</AddressLine2>
			<AddressLine3>Athlone</AddressLine3>
			<AddressLine4>Westmeath</AddressLine4>
			<CountryCode>IE</CountryCode>
		</CollectionAddress>
		<References>
			<Reference>
				<ReferenceName>ShippingTransactionID</ReferenceName>
				<ReferenceValue>123</ReferenceValue>
				<ParcelNumber>1</ParcelNumber>
			</Reference>
		</References>
	</Consignment>
</PreAdvice>';
    }
}
