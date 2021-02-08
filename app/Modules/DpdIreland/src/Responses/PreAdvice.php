<?php


namespace App\Modules\DpdIreland\src\Responses;


/**
 * Class PreAdvice
 * @package App\Modules\Dpd\src\Responses
 */
class PreAdvice extends XmlResponse
{
    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->status() === 'OK';
    }

    /**
     * @return string
     */
    public function status(): string
    {
        return $this->getAttribute('Status');
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
}
