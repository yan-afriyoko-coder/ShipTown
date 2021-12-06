<?php

namespace App\Modules\DpdIreland\src\Responses;

/**
 * Class PreAdvice.
 */
class PreAdvice extends XmlResponse
{
    /**
     * @return bool
     */
    public function isNotSuccess(): bool
    {
        return !$this->isSuccess();
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return ($this->status() === 'OK') && ($this->receivedConsignmentsNumber() > 0);
    }

    /**
     * @return string
     */
    public function status(): string
    {
        return $this->getAttribute('Status');
    }

    /**
     * @return int
     */
    public function receivedConsignmentsNumber(): int
    {
        return (int) $this->simpleXmlArray->ReceivedConsignmentsNumber;
    }

    /**
     * @return array
     */
    public function consignment(): array
    {
        return (array) $this->simpleXmlArray->Consignment;
    }

    /**
     * @return string
     */
    public function trackingNumber(): string
    {
        return $this->getAttribute('TrackingNumber');
    }

    /**
     * @return string
     */
    public function labelImage(): string
    {
        return $this->getAttribute('LabelImage');
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'Status'                     => (string) $this->simpleXmlArray->Status,
            'PreAdviceErrorCode'         => (array) $this->simpleXmlArray->PreAdviceErrorCode,
            'PreAdviceErrorDetails'      => $this->simpleXmlArray->PreAdviceErrorDetails ? (array) $this->simpleXmlArray->PreAdviceErrorDetails : '',
            'ReceivedConsignmentsNumber' => (int) $this->simpleXmlArray->ReceivedConsignmentsNumber,
            'Consignment'                => (array) $this->simpleXmlArray->Consignment,
        ];
    }
}
