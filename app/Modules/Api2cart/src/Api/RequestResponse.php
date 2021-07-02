<?php

namespace App\Modules\Api2cart\src\Api;

use Psr\Http\Message\ResponseInterface;

class RequestResponse
{
    const RETURN_CODE_OK = 0;
    const RETURN_CODE_INCORRECT_API_KEY_OR_IP_ADDRESS = 2;
    const RETURN_CODE_EXCEEDED_CONCURRENT_API_REQUESTS_PER_STORE = 7;
    const RETURN_CODE_MODEL_NOT_FOUND = 112;
    const RETURN_CODE_REQUIRED_PARAMETER_IS_NOT_SPECIFIED = 114;
    const RETURN_CODE_PRODUCT_SKU_MUST_BE_UNIQUE = 113;

    /**
     * @var ResponseInterface
     */
    private $response;
    /**
     * @var string
     */
    private $response_content;

    /**
     * Api2CartResponse constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;

        // used casting because PSR-7
        // https://stackoverflow.com/questions/30549226/guzzlehttp-how-get-the-body-of-a-response-from-guzzle-6
        $this->response_content = $response->getBody()->getContents();
    }

    /**
     * @return string
     */
    public function getAsJson()
    {
        return $this->response_content;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponseRaw()
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return ($this->response->getStatusCode() == 200) && ($this->isReturnCodeOK());
    }

    /**
     * @return bool
     */
    public function isNotSuccess()
    {
        return !$this->isSuccess();
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return json_decode($this->response_content, true);
    }

    /**
     * @return int
     */
    public function getReturnCode()
    {
        return $this->asArray()['return_code'];
    }

    /**
     * @return string
     */
    public function getReturnMessage()
    {
        return $this->asArray()['return_message'];
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->asArray()['result'];
    }

    /**
     * @param int $return_code
     *
     * @return bool
     */
    public function isReturnCode(int $return_code)
    {
        return $this->getReturnCode() == $return_code;
    }

    /**
     * @return bool
     */
    public function isReturnCodeOK()
    {
        return $this->isReturnCode(self::RETURN_CODE_OK);
    }

    /**
     * @return bool
     */
    public function isReturnCodeModelNotFound()
    {
        return $this->isReturnCode(self::RETURN_CODE_MODEL_NOT_FOUND);
    }

    /**
     * @return bool
     */
    public function isReturnCodeProductSkuMustBeUnique()
    {
        return $this->isReturnCode(self::RETURN_CODE_PRODUCT_SKU_MUST_BE_UNIQUE);
    }
}
